from bs4 import BeautifulSoup
import requests

def findTerms():
    response = requests.get('https://central.carleton.ca/prod/bwysched.p_select_term?wsea_code=EXT')
    soup = BeautifulSoup(response.text, 'html.parser')

    options = soup.findAll('tr')[1].findAll('option')
    return [str(opt) for opt in options]