from flask import Flask, render_template, request, jsonify
from app.findCourses import getCourses, createTimetables
from app.getTerms import findTerms

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/findCourses', methods=['POST'])
def findCourses():
    data = request.get_json()
    courses = data['courses']
    term = data['term']
    noEarly = data['noEarly']
    noLate = data['noLate']
    openOnly = data['openOnly']
    daysOff = {int(day) for day in data['daysOff']} if ("none" not in data['daysOff']) else set()
    print('getting courses...')
    return jsonify(courses=createTimetables(getCourses(courses, term, noEarly, noLate, openOnly, daysOff)))

@app.route('/getTerms', methods=['GET'])
def getTerms():
    print('finding terms...')
    return jsonify(terms=findTerms())
    

if __name__ == '__main__':
    app.run(debug=True)