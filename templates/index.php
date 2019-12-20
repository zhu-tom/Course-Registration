<!DOCTYPE html>
<html>
<head>
    <title>Enter Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
                <div class='col'>
                    <input type='button' id='submit' value='Submit' class='btn btn-success'>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Time</th>
                    <th scope="col">Sunday</th>
                    <th scope="col">Monday</th>
                    <th scope="col">Tuesday</th>
                    <th scope="col">Wednesday</th>
                    <th scope="col">Thursday</th>
                    <th scope="col">Friday</th>
                    <th scope="col">Saturday</th>
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
$(document).ready(function(e) {
    for (let i = 8; i <= 21; i++) {
        $('#timetable').append('<tr id="'+ i + '"><th scope="col">' + i + ':00' + '</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
        $('#timetable').append('<tr id="'+ (i+0.5) + '"><th scope="col">' + i + ':30' + '</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
    }
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
    $(document).on('click', '.del-course', (e) => {
        $(e.target).parents('.row').remove();
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

        data = {courses: courses, term: $('#term').val()}
        $.ajax({
            url: '/findCourses',
            type: 'POST',
            contentType: "application/json",
            data: JSON.stringify(data),
            success: (result) => {
                console.log(result);
            }
        });
    });
});
</script>
</html>