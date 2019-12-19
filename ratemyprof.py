from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time


def getRatings(name):
    driver = webdriver.Firefox()
    driver.get('https://www.ratemyprofessors.com/campusRatings.jsp?sid=1420')
    closebtn = driver.find_element_by_class_name('close-this')
    closebtn.click()
    schoolnav = driver.find_element_by_id('schoolNav')
    schoolnav.click()
    element = driver.find_element_by_id('professor-name')
    element.send_keys(name)
    sidepanel = driver.find_element_by_class_name('side-panel')
    results = sidepanel.find_element_by_class_name('result-list')
    element.click()
    firstResult = results.find_element_by_tag_name('a')
    link = 'https://www.ratemyprofessors.com'+ firstResult.get_attribute('href')
    firstResult.click()
    grade = driver.find_element_by_xpath('//div[contains(@class, "quality")]//div[@class="grade"]').text
    takeAgain = driver.find_element_by_xpath('//div[contains(@class, "takeAgain")]//div[@class="grade"]').text
    difficulty = driver.find_element_by_xpath('//div[contains(@class, "difficulty")]//div[@class="grade"]').text
    driver.quit()
    return {'link': link, 'grade': grade, 'takeAgain': takeAgain, 'difficulty': difficulty}
