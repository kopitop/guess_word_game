
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./realtime');

$(document).ready(function() {
    //User Chart
    if($('#myChart').length > 0) {
        var ctx = $("#myChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Room 1", "Room 2", "Room 3", "Room 4"],
                datasets: [{
                    label: 'Score',
                    data: [5, 3, 3, 1,],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    //Sidebar menu
    $('ul').has('li.active').show();

    $('ul.sidebar li ul li').on('click', function (e) {
        e.stopPropagation();
    });

    $('ul.sidebar>li').on('click', function (e) {
        $('.sidebar li ul').each(function (index, element) {
            if ($(element).has('li.active').length === 0) {
                $(element).hide(400);
            };
        })
        $(this).find('ul').show(400);
    });

    $(document).on('click', function (e) {
        if ($('.sidebar').has(e.target).length === 0 && e.target) {
            $('.sidebar li ul').each(function (index, element) {
                if ($(element).has('li.active').length === 0) {
                    $(element).hide(400);
                };
            })
        }
    });

    $(document).on('click', '.status', function () {
        $('.status').on('click', function () {
            $(this).hide(400);
        });
    });

    //Join button
    $('.room-item').on('click', function () {
        $('#join-button').attr('href', laroute.route('rooms.join', { id: $(this).data('room-id') }));
    });

    //Init wPaint
    if($('#wPaint').length > 0) {
        $('#wPaint').wPaint();
    }

    //Status
    if (typeof roomStatus != 'undefined' && roomStatus == 3) {
        $('.is-ready').html('<button class="btn btn-success btn-sm pull-right">' + playingButton + '</button>');
    }

    if ($("#chat-message").length) {
        $("#chat-message").scrollTop($("#chat").height());
    }
})
