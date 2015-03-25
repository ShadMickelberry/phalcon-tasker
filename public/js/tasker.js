/**
 * Created by shad on 11/12/14.
 */

/**
 * Angular JS module
 * @type {*}
 */
var app = angular.module('taskerApp', []);

app.controller('taskerController', function($scope, $http){


});


/**
 * Jquery
 */
$(document).ready(function(){

    $('#tasks').DataTable({
        responsive: {
            details: {
                type: 'column',
                target: -1
            }
        },
        lengthMenu: [[-1, 10, 25], ["All", 10, 25]],
        order: [[5, "asc"]],
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   -1
        },
            { "width": "20px", "targets": 0 },
            { "width": "10%", "targets": 1, "className": "desktop" },
            { "width": "10%", "targets": 2, "className": "desktop" },
            { "width": "20%", "targets": 3 },
            { "width": "20%", "targets": 4 },
            { "width": "20%", "targets": 5, "className": "all" },
        ]
    });

    $('.task_name').each(function() {
        var $this = $(this);

        $this.editable('/tasks/updateField', {
                submitdata : {data_id: $this.attr('data-id'),
                    data_field: $this.attr('data-field')},
                'type'      : 'textarea',
                'rows'      : 6,
                'submit'    : '&#x2714;',
                'cancel'    : '&#x2716;',
                'onblur'    : 'cancel'
            })
    });

    $('.task_priority').each(function(){
        var $this = $(this);
        if ($this.text() == 'Top') {
            $this.css('color', 'red');
        }
            $this.editable('/tasks/updateField', {
                submitdata : {data_id: $this.attr('data-id'),
                              data_field: $this.attr('data-field')},
                data     : " {'Top':'Top','Medium':'Medium','Low':'Low'}",
                type        : 'select',
                onblur      : 'submit'
            })
    });

    var d = new Date();
    var t = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
    var day = d.getDate();
    var year = d.getFullYear();
    var month = d.getMonth()+1;
        if(month <10){
            month = '0'+month;
        }
        if(day <10){
            day = '0'+day;
        }

    var today = year + month + day ;

    var day = t.getDate();
    var year = t.getFullYear();
    var month = t.getMonth()+1;
        if(month <10){
            month = '0'+month;
        }
        if(day <10){
            day = '0'+day;
        }

    var tomorrow = year + month + day ;

    $('.due_date').each(function() {
        var $this = $(this);
        var date = $this.attr('value');
        if (date <= tomorrow) {
            $this.attr('title', 'This task is due tomorrow');
            $this.css('color', 'orange');
        }
        if (date <= today) {
            $this.attr('title', 'This task is overdue');
            $this.css('color', 'red');
        }

            $this.editable('/tasks/updateField', {
                submitdata : {data_id: $this.attr('data-id'),
                        data_field: $this.attr('data-field')},
                loadurl     : '/tasks/getDates',
                type        : 'select',
                onblur      : 'submit'
            })
    });

    $('.due_time').each(function(){
        var $this = $(this);

        $this.editable('/tasks/updateField', {
            submitdata : {data_id: $this.attr('data-id'),
                          data_field: $this.attr('data-field')},
            loadurl     : '/tasks/getTimes',
            type        : 'select',
            onblur      : 'submit'
        })
    });

    $('.complete_task').click(function(){
        var data = {};
        data.data_id = $(this).attr('data-id');
        var button = $(this);
        var row = button.closest('tr');
        $.post('/tasks/close', data).success(function(data){
            console.log(data);
            var json = $.parseJSON(data);
            if(json.success == 'true'){
                $(button).attr('value', 'Set Completed');
                $(row).css('background-color','black');
                $(row).remove();
                //   alert(json.msg);

            }
            else{
                alert(json.msg);}
        });
    });

    $('.complete_project').click(function(){
       alert('Hey, you are about to mark every task in this project as complete.\n\nAre you sure?');
       alert('Are you really really sure?');
        var data = {};
        data.data_id = $(this).attr('data-id');
        var button = $(this);
        var panel = button.closest('div.panel');
        $.post('projects/close', data).success(function(data){
            console.log(data);
            var json = $.parseJSON(data);
            if(json.success == 'true'){

                $(panel).remove();
                //   alert(json.msg);

            }
            else{
                alert(json.msg);}
        });
    });



});


