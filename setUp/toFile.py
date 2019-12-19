import requests
def profsToFile():
    f = open('profs.json', 'w')
    f.write('[')
    schoolId = "1420"
    url = f"http://www.ratemyprofessors.com/filter/professor/?&page=1&filter=teacherlastname_sort_s+asc&query=*%3A*&queryoption=TEACHER&queryBy=schoolId&sid={schoolId}"
    data = requests.get(url).json()
    numProfs = data['searchResultsTotal']

    for i in range(1, numProfs+1):
        url = f"http://www.ratemyprofessors.com/filter/professor/?&page={i}&filter=teacherlastname_sort_s+asc&query=*%3A*&queryoption=TEACHER&queryBy=schoolId&sid={schoolId}"
        data = requests.get(url).json()['professors']
        for info in data:
            f.write(f"{info},")
    f.write(']')
    f.close()
profsToFile()