<!DOCTYPE html>
<html>
<head>
    <title>Enter Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        table {
            table-layout: fixed;
        }
        #timetable td {
            color: white;
        }
        tbody tr {
            height: 25px;
        }
        td h6, p {
            margin: 0;
            padding: 0;
            border: 0;
        }
        td h6 {
            font-size:1em;
        }
        .small {
            font-size: 0.7em;
        }
        .time {
            font-weight: normal;
        }
        td {
            vertical-align: center;
        }
    </style>
</head>
<body>
    <div class='container'>
        <form id='courses'>
            <div class='form-row form-group'>
                <div class='col-2'>
                    <label for='term' class='form-label'>Term</label>
                </div>
                <div class='col'>
                    <select id='term' class='custom-select'>
                        <option value='' selected disabled>Select a Term</option>
                    </select>
                </div>
            </div>
            <div class='form-row form-group'>
                <div class='col-2'>
                    <label for='course' class='form-label'>Course Code</label>
                </div>
                <div id='codes' class='col'>
                    <div class='row form-group'>
                        <div class='col'>
                            <input type='text' class='form-control' name='course' placeholder='Course Code'>
                        </div>
                        <div class='col-2'>
                            <input type='button' style='width:100%' class='btn btn-primary add-course' value='Add Another'>
                        </div>
                    </div>
                </div>
            </div>
            <div class='form-row form-group'>
                <div class='col-2'>
                    <label class='form-label'>Filters</label>
                </div>
                <div class='col'>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="noEarly" value="option1">
                        <label class="form-check-label" for="noEarly">No 8:35am Starts</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="noLate" value="option2">
                        <label class="form-check-label" for="noLate">No 8:55pm Endings</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="openOnly" value="option3">
                        <label class="form-check-label" for="openOnly">Open Classes Only</label>
                    </div>
                </div>
            </div>
            <div class='form-row form-group'>
                <div class='col'>
                    <input type='button' id='submit' value='Search' class='btn btn-success'>
                </div>
            </div>
        </form>
        <hr/>
        <div class="d-flex justify-content-center">
            <div id='spinner' class="spinner-border" style='display:none' role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class='row' id='pageNav'>
            <div class='col-3'>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" aria-label="Previous">
                                <span aria-hidden="true" aria-label='Previous'>&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                        <input type='number' id='page'>
                        <li class="page-item">
                            <a class="page-link" aria-label="Next">
                                <span aria-hidden="true" aria-label='Next'>&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class='col-2 align-middle text-left' id='totPages'>
            </div>
            <div class='col'>
            </div>
        </div>
        <div id='currClasses' class="btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-outline-primary" data-toggle="button" aria-pressed="false">Class 1</button>
        </div>
        <table id='weekly' class="table-sm table table-bordered">
            <thead>
                <tr>
                    <th class='text-center' style='width:60px' cope="col">Time</th>
                    <th class='text-center' scope="col">Sunday</th>
                    <th class='text-center' scope="col">Monday</th>
                    <th class='text-center' scope="col">Tuesday</th>
                    <th class='text-center' scope="col">Wednesday</th>
                    <th class='text-center' scope="col">Thursday</th>
                    <th class='text-center' scope="col">Friday</th>
                    <th class='text-center' scope="col">Saturday</th>
                </tr>
            </thead>
            <tbody id='timetable'>

            </tbody>
        </table>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>

codeRow = "<div class='row form-group'>\
                <div class='col'>\
                    <input type='text' class='form-control' name='course' placeholder='Course Code'>\
                </div>\
                <div class='col-2'>\
                    <input type='button' style='width:100%' class='btn btn-primary add-course' value='Add Another'>\
                </div>\
            </div>"
delbtn = "<input type='button' style='width:100%' class='btn btn-danger del-course' value='Delete'>"


function loadTimetable() { // loads timetable template
    $('#timetable').text('');
    $('#timetable').append('<tr id="7.5"><th class="time align-middle text-right" rowspan="2" scope="col">8:00</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
    for (let i = 8; i <= 21; i++) {
        $('#timetable').append('<tr id="'+ i + '"><th class="time align-middle text-right" style="display:none" scope="col"></th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
        $('#timetable').append('<tr id="'+ (i+0.5) + '"><th class="time align-middle text-right" rowspan="2" scope="col">' + (i+1) + ':00' + '</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
    }
    $('#timetable').append('<tr id="22"><th class="time align-middle text-right" style="display:none" scope="col"></th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');

}

var timetables = null;
var count = 0;

function loadPage(pageNum) { // fills timetable wit courses
    $('#page').val(pageNum+1);
    result = timetables;
    loadTimetable();
    colours = ['#CC0000', '#FF8800', '#007E33', '#0099CC', '#00695c', '#0d47a1', '#9933CC', '#1C2331', '#3e2723', '#880e4f']
    rows = $('#timetable').find('tr');
    
    for (lecture of result['courses'][pageNum]) {
        if (lecture != null) {
            startTime = lecture.time.values[0];
            hour = Math.floor(startTime/100);
            mins = (startTime % 100 == 35) ? 0.5 : 0;
            startTime = hour + mins;

            endTime = lecture.time.values[1];
            hour = Math.floor(endTime/100);
            mins = (endTime % 100 == 25) ? 0.5 : 1;
            endTime = hour + mins;

            colour = colours.pop();
            rowspan = (endTime-startTime)/0.5;
            startRow = $('[id="'+ startTime + '"]');
            for (day of lecture.days) {
                for (let i = startTime+0.5; i < endTime; i+=0.5) {
                    $($('[id="'+ i + '"]').find('td')[day]).css('display', 'none');
                }
                $($(startRow).find('td')[day])
                .attr('rowspan', rowspan)
                .attr('id', lecture.code+lecture.section)
                .css('background-color', colour)
                .append('<h6 style="display: inline;">' + lecture.code + lecture.section + '</h6>' + (lecture.building.abbrev ?  ' <p style="display: inline; font-size: 0.8rem;" data-placement="top" data-toggle="tooltip" title="' + lecture.building.name + '">(' + lecture.building.abbrev + ')</p>' : '') + '<p class="small">' + lecture.time.display + ' (' + lecture.crn + ')' + '</p><p class="small prof">'+ lecture.prof.name + (lecture.prof.ratings.overall_rating ? ' (' + lecture.prof.ratings.overall_rating + ')' : '') +'</p>');
            }
        }
        
    }
}

$(document).ready(function(e) {
    loadTimetable();

    $.ajax({
        url: '/getTerms',
        type: 'POST',
        contentType: 'application/json',
        success: (result) => {
            for (el of result.terms) {
                $('#term').append(el);
            }
            $($('#term').children()[1]).removeAttr('selected');
            $('#term').val('');
        }
    });
    $(document).on('click', '.add-course', (e) => {
        $(e.target).after(delbtn);
        $(e.target).remove();
        $('#codes').append(codeRow);
    });
    $(document).on('input', '#page', (e) => {
        count = $(e.target).val()-1
        if (count < 0) {
            count = timetables.courses.length - 1;
        }
        else if (count >= timetables.courses.length) {
            count = 0;
        }
        loadPage(count);
    });
    $(document).on('click', '.del-course', (e) => {
        $(e.target).parents('.row').remove();
    });
    $(document).on('click', '.page-link', (e) => {
        count += $(e.target).attr('aria-label') == 'Next' ? 1 : -1;
        if (count < 0) {
            count = timetables.courses.length - 1;
        }
        else if (count >= timetables.courses.length) {
            count = 0;
        }
        loadPage(count);
    });
    $('#submit').on('click', (event) => {
        if ($('#term').val() === null) {
            $('#term').addClass('is-invalid');
            return null;
        }

        courses = [];
        for (input of $('input[name=course]')) {
            if ($(input).val() != '') {
                courses.push($(input).val());
            }
        }

        data = {courses: courses, term: $('#term').val(), noEarly: $('#noEarly').prop('checked'), noLate: $('#noLate').prop('checked'), openOnly: $('#openOnly').prop('checked')}
        $.ajax({
            url: '/findCourses',
            type: 'POST',
            contentType: "application/json",
            data: JSON.stringify(data),
            beforeSend: () => {
                $('#spinner').css('display', 'block');
                $('#weekly').css('display', 'none');
                $('#pageNav').css('display', 'none')
            },
            success: (result) => {
                console.log(result);
                $('#totPages').text('of ' + result.courses.length);
                timetables = result;
                if (result['courses'].length === 0) {
                    alert('No Timetables Found');
                    return;
                }
                loadPage(0);
            },
            complete: () => {
                $('#spinner').css('display', 'none');
                $('#weekly').css('display', 'table');
                $('#pageNav').css('display', 'flex');
            },
            error: (jqXHR, textStatus, errorThrown) => {
                alert(textStatus);
            }
        });
    });
});
</script>
</html>