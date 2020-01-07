import mechanicalsoup
from bs4 import BeautifulSoup
from findProfs import findProf

def product(lis):
    if lis == []:
        return 1
    if len(lis) == 1:
        return lis[0]
    return lis[0] * product(lis[1:])

def checkTimetable(timetable):
    times = [set(range(section['time']['values'][0], section['time']['values'][1]+1)) if section != None and section['time'] != None and section['time']['values'][0] != None else None for section in timetable]
    days = [set(section['days']) if section != None else None for section in timetable]
    for a in range(len(days)):
        if days[a] != None and times[a] != None:
            for b in range(a+1, len(days)):
                if days[b] != None and times[b] != None:
                    if len(days[a].intersection(days[b])) != 0 and len(times[a].intersection(times[b])) != 0: # days match and times overlap
                        return False
    return True

def addTutorials(timetable):
    n = len(timetable)
    counter = [0 for _ in range(len(timetable))]
    tutorials = []
    validTimetables = []
    temp = timetable[:]
    #print(product([len(lecture['additional']) for lecture in timetable if lecture['additional'] != []]))
    for _ in range(product([len(lecture['additional']) for lecture in timetable if lecture['additional'] != []])): #all possibilities
        for i in range(n): # iterate through courses
            if timetable[i]['additional'] != []:
                tutorials.append(timetable[i]['additional'][counter[i]]) # add course based on current counter
            else:
                tutorials.append(None)
            if i == n-1: # add last course in timetable
                k = i
                counter[k] += 1 # add to counter at last element (ones column)
                while counter[k] >= len(timetable[k]['additional']): # carry over one if max exceeded
                    counter[k] = 0
                    k -= 1
                    if k < 0:
                        break
                    counter[k] += 1
                temp += tutorials
                if checkTimetable(temp):
                    validTimetables.append(temp)
                tutorials = []
                temp = timetable[:]
    return validTimetables


def createTimetables(courses):
    validTimetables = []
    counter = [0 for _ in range(len(courses))]
    timetable = []
    n = len(courses)
    #print(product([len(lectures) for lectures in courses]))

    for _ in range(product([len(lectures) for lectures in courses])): #all possibilities
        for i in range(n): # iterate through courses
            timetable.append(courses[i][counter[i]]) # add course based on current counter
            if i == n-1: # add last course in timetable
                k = i
                counter[k] += 1 # add to counter at last element (ones column)
                while counter[k] == len(courses[k]): # carry over one if max exceeded
                    counter[k] = 0
                    k -= 1
                    if k < 0: 
                        break
                    counter[k] += 1
                validTimetables += addTutorials(timetable)
                timetable = []
    return validTimetables

def getCourses(codes, term, noEarly, noLate, openOnly):
    return [findCourse(code, term, noEarly, noLate, openOnly) for code in codes]

def findCourse(code, term, noEarly, noLate, openOnly):
    browser = mechanicalsoup.StatefulBrowser()
    browser.open("https://central.carleton.ca/prod/bwysched.p_select_term?wsea_code=EXT")
    browser.select_form('form[action="bwysched.p_search_fields"]')
    browser['term_code'] = term
    response = browser.submit_selected()

    browser.select_form('form[action="bwysched.p_course_search"]')
    form = browser.get_current_form()
    form.set_select({'sel_subj':code[:4]})
    form.set_select({'sel_special':'O'}) if openOnly else None
    browser['sel_number'] = code[4:]
    response = browser.submit_selected()
    html = browser.get_current_page()
    rows = html.find('form', {'action':"bwysched.p_list_sections_chk"}).find('table').findAll('tr')[4].find('td').find('div').find('table').findAll('tr')

    buildingAbbrev = {'AlgonC AdvancedTechnologyCtr': 'ATC', 'Architecture Building': 'AA', 'Athletics': 'AC', 'Alumni Hall': 'AH', 'Azrieli Pavilion': 'AP', 'ARISE Building': 'AR', 'Azrieli Theatre':'AT', 'Canal Building': 'CB', 'Residence Commons':'CO', 'Dominion-Chalmers Centre':'DC', 'Dunton Tower':'DT', 'Fieldhouse': 'FH', 'Gymnasium': 'GY', 'Human Computer Interaction Building': 'HC', 'Herzberg Labs for Physics': 'HP', 'Health Science Building':'HS', 'Ice House':'IH', 'Loeb Building':'LA', 'Maintenance Building':'MB', 'Minto Centre':'MC', 'Mackenzie Building':'ME', 'MacOdrum Library':'ML', 'University of Ottawa':'UO', 'Nesbitt Building':'NB', 'Nichols Building':'NI', 'National Wildlife Research Centre':'NW', 'Paterson Hall':'PA', 'Richcraft Hall':'RB','Robertson Hall':'RO','Southam Hall':'SA','Steacie Building':'SC',"St. Patrick's Building":'SP', 'Social Sciences Building':'SR', 'Tory Building':'TR', 'Technology and Training Centre':'TT','University Centre':'UC','Visualization and Simulation Building':'VS'}
    key = {1:'status', 2:'crn', 4:'section', 7:'type', 10:'prof'}
    infoKey = {1:'days', 2:'time', 3:'building', 4:'room'}
    daysKey = {'Sun': 0, 'Mon': 1, 'Tue': 2, 'Wed': 3, 'Thu': 4, 'Fri': 5, 'Sat': 6}
    sections = []
    extras = []
    lastColour = ''

    for j in range(len(rows)):
        currColour = rows[j]['bgcolor']
        if lastColour != currColour:
            lastColour = currColour
            section = {'code': code}
            
            cols = rows[j].findAll('td')
            classFull = False
            for i in range(len(cols)):
                if i in key.keys():
                    section[key[i]] = cols[i].text.strip()
                    if i == 10:
                        section['prof'] = {'name': section['prof'], 'ratings': findProf(section['prof'])}
                    elif i == 1 and openOnly and section['status'] != 'Open': 
                        classFull = True
                        break
            if classFull:
                continue
            
            info = rows[j+1].findAll('td')[1].findAll('b')
            badTime = False
            for i in range(len(info)):
                if i in infoKey.keys():
                    if info[i].next_sibling != None:
                        section[infoKey[i]] = info[i].next_sibling.strip()
                    else:
                        section[infoKey[i]] = None
                
                    if i == 1:
                        section['days'] = [daysKey[day] if day != '' else None for day in section['days'].strip().split(' ')]
                    elif i == 2 and section['time'] != None:
                        section['time'] = {'display': section['time'], 'values': [int(''.join(time.split(":"))) if time != '' else None for time in section['time'].split(' - ')]}
                        if section['time']['values'] != [None]:
                            if (noEarly and section['time']['values'][0] == 835) or (noLate and section['time']['values'][1] == 2055):
                                badTime = True
                                break
                    elif i == 3 and section['building'] != '':
                        print(f'Building Name: {section["building"]}')
                        section['building'] = {'name': section['building'], 'abbrev': buildingAbbrev[section['building']]}

            if badTime:
                continue

            try:
                alsoRegIn = rows[j+2].findAll('td')[1].find('b')
                if alsoRegIn.text == 'Also Register in:':
                    extraCourses = alsoRegIn.next_sibling.strip().replace(code[:4] + ' ' + code[4:], '').split(' or ')
                    extraCourses[0] = extraCourses[0].strip()
                    section['extras'] = extraCourses
                else:
                    section['extras'] = None

                sections.append(section)
            except IndexError:
                pass

    new = []
    copy = sections[:]
    for section in sections:
        section['additional'] = []
        if len(section['section']) == 1:
            new.append(section)
            copy.remove(section)
 
    for section in new:
        for extra in copy:
            if section['section'] in extra['extras']:
                section['additional'].append(extra)

    return new