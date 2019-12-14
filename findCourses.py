# from flask import Flask

# app = Flask(__name__)

# @app.route('/')

import mechanicalsoup
from bs4 import BeautifulSoup

browser = mechanicalsoup.StatefulBrowser()
browser.open("https://central.carleton.ca/prod/bwysched.p_select_term?wsea_code=EXT")
browser.select_form('form[action="bwysched.p_search_fields"]')
browser['term_code'] = '202010'
response = browser.submit_selected()


browser.select_form('form[action="bwysched.p_course_search"]')
browser['sel_subj'] = 'COMP'
browser['sel_number'] = '1406'
response = browser.submit_selected()
html = browser.get_current_page()
rows = html.find('form', {'action':"bwysched.p_list_sections_chk"}).find('table').findAll('tr')[4].find('td').find('div').find('table').findAll('tr')
key = {1:'status', 2:'crn', 4:'section', 7:'type', 10:'prof'}
infoKey = {1:'days', 2:'time', 3:'building', 4:'room'}
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
                section[key[i]] = cols[i].text
        
        info = rows[j+1].findAll('td')[1].findAll('b')
        for i in range(len(info)):
            if i in infoKey.keys():
                section[infoKey[i]] = info[i].next_sibling.strip()
        sections.append(section)
print(sections)
            

