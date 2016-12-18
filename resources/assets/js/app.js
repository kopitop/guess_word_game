
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
                labels: chartData.labels,
                datasets: [{
                    label: 'Score',
                    data: chartData.data,
                    backgroundColor: chartData.bgColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            stepSize: 1,
                        },
                        scaleLabel :{
                            labelString: chartData.trans.score,
                            display: true,
                        },
                    }],
                    xAxes: [{
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel :{
                            labelString: chartData.trans.room,
                            display: true,
                        },
                    }]
                },

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

    //Prompt confirm dialog
    $(".confirm").on('click', function () {
        return confirm(confirmation);
    });
})
