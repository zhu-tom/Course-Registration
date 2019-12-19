import mechanicalsoup
from bs4 import BeautifulSoup
from findProfs import findProf

def getCourses(codes, term):
    return [findCourse(code, term) for code in codes]

def findCourse(code, term):
    browser = mechanicalsoup.StatefulBrowser()
    browser.open("https://central.carleton.ca/prod/bwysched.p_select_term?wsea_code=EXT")
    browser.select_form('form[action="bwysched.p_search_fields"]')
    browser['term_code'] = term
    response = browser.submit_selected()

    browser.select_form('form[action="bwysched.p_course_search"]')
    browser['sel_subj'] = code[:4]
    browser['sel_number'] = code[4:]
    response = browser.submit_selected()
    html = browser.get_current_page()
    rows = html.find('form', {'action':"bwysched.p_list_sections_chk"}).find('table').findAll('tr')[4].find('td').find('div').find('table').findAll('tr')

    key = {1:'status', 2:'crn', 4:'section', 7:'type', 10:'prof'}
    infoKey = {1:'days', 2:'time', 3:'building', 4:'room'}
    daysKey = {'Sun': 0, 'Mon': 1, 'Tue': 2, 'Wed': 3, 'Thu': 4, 'Fri': 5, 'Sat': 6}
    sections = []
    lastColour = ''

    for j in range(len(rows)):
        currColour = rows[j]['bgcolor']
        if lastColour != currColour:
            lastColour = currColour
            section = {}
            
            cols = rows[j].findAll('td')
            for i in range(len(cols)):
                if i in key.keys():
                    section[key[i]] = cols[i].text.strip()
                    if i == 10:
                        section['prof'] = {'name': section['prof'], 'ratings': findProf(section['prof'])}
            
            info = rows[j+1].findAll('td')[1].findAll('b')
            for i in range(len(info)):
                if i in infoKey.keys():
                    section[infoKey[i]] = info[i].next_sibling.strip()
                    if i == 1:
                        section['days'] = [daysKey[day] for day in section['days'].split(' ')]
                    elif i == 2:
                        section['time'] = [str(''.join(time.split(":"))) for time in section['time'].split(' - ')]

            if len(section['section']) > 1:
                for i in range(len(sections)):
                    if sections[i]['section'] == section['section'][0]:
                        sections[i]['additional'].append(section)
            else:
                section['additional'] = []
                sections.append(section)
    return sections
            

