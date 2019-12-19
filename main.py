from flask import Flask, render_template, request, jsonify
from findCourses import getCourses
from getTerms import findTerms

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.php')

@app.route('/findCourses', methods=['POST'])
def findCourses():
    data = request.get_json()
    courses = data['courses']
    term = data['term']
    print('getting courses...')
    return jsonify(courses=getCourses(courses, term))

@app.route('/getTerms', methods=['POST'])
def getTerms():
    return jsonify(terms=findTerms())
    

if __name__ == '__main__':
    app.run(debug=True)