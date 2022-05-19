<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task</title>
     <!-- CSRF Token -->
     <meta name="_token" content="{{ csrf_token() }}">
     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <!-- Include Google font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,300,600"> 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
</head>
<body>
    <div class='container'>
        <div class="row justify-content-md-center">
            <form method="post" id="datepicker_form" class="form-horizontal" role="form">
                @csrf
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="text" class="form-control datepicker" id="start_date" aria-describedby="emailHelp" placeholder="Start Date" name="start_date" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="text" class="form-control datepicker" id="end_date" placeholder="End Date" name="end_date" required autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary" id="btn_submit">Submit</button>
                <button type='button' onclick='destroy()' class='btn btn-danger'>Destroy</button>
            </form>
        </div>
        <br><br>
        <div style='display:none;' id='tbl_asteroids_data'>
            <table border='1' class='table'>
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Fastest Asteroid (km/h)</th>
                        <th>Closest Asteroid</th>
                        <th>Avg Size of Asteroid in km</th>
                    </tr>
                    <tbody id='tbody_asteroid'>

                    </tbody>
                </thead>
            </table>
        </div>
        <div style='width:1000px !important;height:200px !important;'>
        <canvas id="myChart" width="768" height="400"></canvas>
        </div>
    </div>
    
</body>
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

        var minDate ;
        var maxDate;
        var mDate
        // var j = jQuery.noConflict();
        $( "#start_date" ).datepicker({
            onSelect: function() {
            minDate = $( "#start_date" ).datepicker("getDate");
            var mDate = new Date(minDate.setDate(minDate.getDate()));
            maxDate = new Date(minDate.setDate(minDate.getDate() + 6));
            $("#end_date").datepicker("setDate", maxDate);
            $( "#end_date" ).datepicker( "option", "minDate", mDate);
            $( "#end_date" ).datepicker( "option", "maxDate", maxDate);
        }
    });
    var tdate = new Date();
    var ddate = new Date(tdate.setDate(tdate.getDate() + 6));
    $("#start_date").datepicker("setDate", new Date());
    $( "#end_date" ).datepicker();
    $("#end_date").datepicker("setDate",ddate);

    var myChart = null;
    var data = null;

    myChart = new Chart(
        document.getElementById('myChart'),
        {
            type: 'line',
            data:{
            },
            options: {}
        }
    );

    //submit form event
    $("#datepicker_form").on('submit', function(e){
        e.preventDefault();
        var form_data = $(this).serialize();
        $.ajax({
            url:"{{ url('submit_datepicker') }}",
            data:form_data,
            type:"POST",
            dataType:"JSON",
            success: function(res){
                console.log(res);
                if(res.status == false) {
                    alert(res.message);
                    return false;
                } else {
                    $("#myChart").css('display','block');
                    $("#tbl_asteroids_data").css('display','block');
                    $("#tbody_asteroid").html("<tr><td>1</td><td>ID: "+res.Fastest_Asteroid.id+" Speed:"+res.Fastest_Asteroid.speed+"</td><td>ID: "+res.closest_asteroid.id+" Distance: "+res.closest_asteroid.distance+"</td><td>"+res.avg_size_of_asteroid+"</td></tr>");
                    data = {
                        labels: res.graph_dates_arr,
                        datasets: [{
                            label: 'No of Asteroids Per Date',
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                            data: res.total_count_objs,
                        }],
                    }
                    
                    myChart.data = data;
                    myChart.update(); 
                }
            }
        });
    });  
  });

  function destroy() {
    $("#datepicker_form")[0].reset();
    var $dates = $('#start_date, #end_date').datepicker(); 
    $dates.attr('value', '');
    $dates.datepicker( "option" , {
        minDate: null,
        maxDate: null} 
        );
    $("#tbl_asteroids_data").css('display','none');
    $("#tbody_asteroid").html(''); 
    // setTimeout(() => {
    //     location.reload();
    // }, 5000);
    $("#myChart").css('display','none');
  }
  </script>
</html>