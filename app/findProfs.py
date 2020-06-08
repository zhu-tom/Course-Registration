import requests
import ast

f = open('profs.txt', 'r')
profs = ast.literal_eval(f.readline())
f.close()

def findProf(name):
    name = name.split(" ")
    schoolId = "1420"
    
    for prof in profs:
        if prof['tLname'] == name[-1]: # last name match
            if prof['tFname'][0] == name[0][0]: # first letter of first name match
                return prof
    return {}
