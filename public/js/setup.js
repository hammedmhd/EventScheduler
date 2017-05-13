/*making all active clients draggable*/
$('.fc-event').each(function() {

    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
        title: $.trim($(this).text()), // use the element's text as the event title
        color: $(this).css('background-color'),
        textColor: $(this).css('color'),
        stick: true
    });

    // make the event draggable using jQuery UI
    $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
    });

});

/*setting the removable area for the active lessons */
 var isEventOverDiv = function(x, y) {

    var external_events = $( '.external-events' );
    var offset = external_events.offset();
    offset.right = external_events.width() + offset.left;
    offset.bottom = external_events.height() + offset.top;

    // Compare
    if (x >= offset.left
        && y >= offset.top
        && x <= offset.right
        && y <= offset .bottom) { return true; }
    return false;

}

/*-----------------------------------------------------------------------*/
var eventSources = [];
var clientList = [];
$.ajax({
	url: 'eventSources',
	type: 'get',
	success: function(response){
		var json = JSON.parse(response);
		eventSources = json.eventSources;

		$('#calendar').fullCalendar({
			header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay,listWeek'
			},
			buttonText: {
				today: 'Today'
			},
			views: {
				month: {
					buttonText: 'Calendar'
				},
				listMonth: {
					buttonText: 'Monthly'
					//listDayFormat: 'dddd',
					//listDayAltFormat: 'MMMM D, YYYY'
				},
				listWeek: {
					buttonText: 'List By Week'
				},
				listDay: {
					buttonText: 'Daily',
					listDayAltFormat: 'MMMM D, YYYY'
				},
				agendaDay:{
					buttonText: 'Daily'
				},
				agendaWeek:{
					buttonText: 'Weekly'
				}
			},
			eventSources: eventSources,
			//timezone: 'UTC',
			firstDay: '6', // if ISO weekNumberCalculation then default is set to 1 Monday, currently ISO is comment only
			showNonCurrentDates: false,
			defaultView: 'agendaWeek',
			//aspectRatio: '500px',
			navLinks: true, // can click day/week names to navigate views
			eventLimit: true, // allow "more" link when too many events
			fixedWeekCount: false, //If false, the calendar will have either 4, 5, or 6 weeks, depending on the month. If true then 6 weeks default.
			noEventsMessage: 'No classes to display, how about a break!',
			timeFormat: 'h(:mm)a',
			defaultTimedEventDuration: '01:00:00',
			//minTime: '0:00:00',
			//maxTime: '22:00:00',
			scrollTime: '09:00:00', //should display the scrolled time on view load 
			//weekNumberCalculation: 'ISO', //if ISO weekNumberCalculation then default is set to 1
			//weekNumbers: true, //displays weeknumber
			//weekends: false, //to hide weekends, weekends: true.
			editable: true,
			//selectable: true,
			droppable: true,
			dragRevertDuration: 0,
			dragScroll: true,
			//eventDurationEditable: false,
			/*select: function(start, end){
				var title = prompt('Client Name:');
				var background = prompt('Label color:');
				var textColor = prompt('Font color:');
				var start = start.format();
				var end = end ? end.format() : null;
				if(title){
					eventData = {
						title: 'temp',
						name: title,
						start: start,
						end: end,
						paid: false,
						color: background,
						textColor: textColor
					};
					$('#calendar').fullCalendar('renderEvent', eventData, true);
					$.ajax({
						url: 'newEvent',
						type: 'get',
						data: eventData,
						success: function(response){
							console.log(response);
						}

					});
				}
				$('#calendar').fullCalendar('unselect');
			}, */
			eventClick: function(event, element, view){
				displayNotification('Schedule with Client: '+ event.title +' on Date: '+ event.start.format('MMMM, D')+' at Time: '+event.start.format('h (mm)a'));
			},
			eventReceive: function(event){//External Event Dropped to calendar
				$('#expenseload').css('display', 'inline');
				var title = event.title;
				var start = event.start.format();
				var end = event.end ? event.end.format() : null;
				var obj = {
					title: title,
					start: start,
					end: end,
					paid: false
				};
				$.ajax({
					url: 'newEvent',
					type: 'get',
					data: obj,
					success: function(response){
						$('#expenseload').css('display', 'none');
						displayNotification(response);
						if(response == 'Looks like this slot is occupied, try another!'){
							$('#calendar').fullCalendar('removeEvents', event._id);
						}else if(response == 'Lesson Added'){
							location.reload();
						}
						//once logic done, customize ur own flash bar to avoid constant reloading.
						/*$('#flash-message').slideDown(1000);
						$('#flash-message').html(response);
						setTimeout(function(){
							$('#flash-message').slideUp(1000);
							$('#flash-message').html('');
						},2000);*/
					}
				});
			},
			eventResize: function(event, delta, revertFunc){//Active Event is resized
				if(confirm('Lesson time has been changed, set new changes?')){
					$('#expenseload').css('display', 'inline');
					var title = event.title;
					var start = event.start.format();
					var end = event.end ? event.end.format() : null;
					var paid = event.paid;
					var obj = {
						title: title,
						start: start,
						end: end,
						paid: paid
					};
					$.ajax({
						url: 'updateEvent',
						type: 'get',
						data: {resize: obj},
						success: function(response){
							displayNotification(response);
							incomingExpenses();
						}
					});
				}else{ revertFunc(); }
			},
			eventDrop: function(event, delta, revertFunc, duration){//dragging and dropping event within calendar
				if(confirm("Lesson date/time has been modified, keep these changes?")){
					$('#expenseload').css('display', 'inline');
					var oldstart = event.start._i;
					var oldend = event.end ? event.end._i : null;
					var title = event.title;
					var start = event.start.format();
					var end = event.end ? event.end.format() : null;
					var paid = event.paid;
					var obj = {
						title: title,
						start: start,
						end: end,
						paid: paid
					};
					var oldevent = {
						start: oldstart,
						end: oldend
					};
					$.ajax({
						url: 'updateEvent',
						type: 'get',
						data: {new: obj, old: oldevent},
						success: function(response){
							$('#expenseload').css('display', 'none');
							displayNotification(response);
							if(response == 'Looks like this slot is occupied, try another!'){
								revertFunc();
							}else if(response == 'Lesson date updated!'){
								location.reload();
							}else{
								revertFunc();
							}
						}
					});
				}else{
					revertFunc();
				}
			},
			eventDragStop: function(event, jsEvent, ui, view){//removing event from calendar
				if(isEventOverDiv(jsEvent.clientX, jsEvent.clientY)) {
					$('#expenseload').css('display', 'inline');
                    var el = $('#calendar').fullCalendar('removeEvents', event._id);
                    var title = event.title;
					var start = event.start.format();
					var end = event.end ? event.end.format() : null;
					var obj = {
						title: title,
						start: start,
						end: end
					};
                    $.ajax({
                    	url: 'removeEvent',
                    	type: 'get',
                    	data: obj,
                    	success: function(response){
                    		displayNotification(response);
                    		incomingExpenses();
                    	}
                    });
                }
			},
			viewRender: function(view, element){
				$('#expenseload').css('display', 'inline');
				var obj = {
					start: view.start.format(),
					end: view.end.format(),
					name: view.name
				};
				$.ajax({
					url: 'viewExpenses',
					type: 'get',
					data: {obj: obj},
					success: function(response){
						var currency = ' AED';
						var total = response.expenses.total+currency;
						var paid = response.expenses.paid+currency;
						var unpaid = response.expenses.unpaid+currency;
						$('#totalamount').html(total);
						$('#paidamount').html(paid);
						$('#unpaidamount').html(unpaid);
						var clients = response.clients;
						var count = 0;
						var body = document.getElementById('payment-body');
						var table = document.getElementById('payment');
						body.innerHTML = '';
						if(clients.length > 0){
							for(c in clients)
							{
								if(clients[c].start.split('T')[1] == undefined) continue;
								count++;
								originalstartString = clients[c].start;	
								clients[c].start = clients[c].start.split('T')[1]; 
								clients[c].paid = clients[c].paid == 'true' ? ' checked="checked" onchange="refreshPayment(this)">' : ' onchange="refreshPayment(this)">';
								body.innerHTML += '<tr start="'+originalstartString+'" name="'+clients[c].title+'"><td>'+clients[c].title+'</td><td>'+clients[c].start+'</td><td><label class="switch"><input type="checkbox"'+clients[c].paid+'<div class="slider"></div></label></td></tr>';
							}
							if(count > 0)
							{	
							 	body.innerHTML += '<tr><td colspan="3" style="border-top:none;"><a id="saveme" class="btn btn-primary" onclick="location.reload()">Save changes</a></td></tr>';
							}else{body.innerHTML = '<td colspan="3"><p>No lessons for this period..</p></td>';}
						}else{ body.innerHTML = '<td colspan="3"><p>No lessons for this period..</p></td>';}
						$('#expenseload').css('display', 'none');
					}
				});
			}
	    });
	}
});

function displayNotification(text)
{
	$('#notify').html(text);
	$('#notify').fadeIn(500, function(){
		setTimeout(function(){
			$('#notify').fadeOut(500, function(){
				$('#notify').html('');
			});
		}, 2500);
	});
}

function refreshPayment(element)
{
	$('#expenseload').css('display', 'inline');
	var data = element.parentElement.parentElement.parentElement;
	var title = data.getAttribute('name');
	var start = data.getAttribute('start');
	var status = element.checked.toString();
	var obj = {
		title: title,
		start: start,
		paid: status
	}
	$.ajax({
		url: 'updatePayment',
		type: 'get',
		data: {obj: obj},
		success: function(){
			//console.log(response);
			incomingExpenses();
		}
	});
}

function incomingExpenses()
{//for the refresh button on expenses sidebar window
	var view = $('#calendar').fullCalendar('getView');
	var obj = {
		start: view.start.format(),
		end: view.end.format(),
		name: view.name
	};
	$.ajax({
		url: 'viewExpenses',
		type: 'get',
		data: {obj: obj},
		success: function(response){
			var currency = ' AED';
			var total = response.expenses.total+currency;
			var paid = response.expenses.paid+currency;
			var unpaid = response.expenses.unpaid+currency;
			$('#totalamount').html(total);
			$('#paidamount').html(paid);
			$('#unpaidamount').html(unpaid);
			var clients = response.clients;
			var count = 0;
			var body = document.getElementById('payment-body');
			body.innerHTML = '';
			if(clients.length > 0){
				for(c in clients)
				{
					if(clients[c].start.split('T')[1] == undefined) continue;
					count++;
					originalstartString = clients[c].start;	
					clients[c].start = clients[c].start.split('T')[1]; 
					clients[c].paid = clients[c].paid == 'true' ? ' checked="checked" onchange="refreshPayment(this)">' : ' onchange="refreshPayment(this)">';
					body.innerHTML += '<tr start="'+originalstartString+'" name="'+clients[c].title+'"><td>'+clients[c].title+'</td><td>'+clients[c].start+'</td><td><label class="switch"><input type="checkbox"'+clients[c].paid+'<div class="slider"></div></label></td></tr>';
				}
				if(count > 0)
				{	
				 	body.innerHTML += '<tr><td colspan="3" style="border-top:none;"><a id="saveme" class="btn btn-primary" onclick="location.reload()">Save changes</a></td></tr>';
				}else{body.innerHTML = '<td colspan="3"><p>No lessons for this period..</p></td>';}
			}else{ body.innerHTML = '<td colspan="3"><p>No lessons for this period..</p></td>';}
			$('#expenseload').css('display', 'none');
		}
	});
}

function editClient(name)
{
	var clientName = name;
	var newBackgroundColor = prompt(clientName+"'s"+' new background color:');
	var newTextColor = prompt(clientName+"'s"+' new text color');
	if(newBackgroundColor == null || newTextColor == null){
		return;
	}

	var obj = {
		title: clientName
	};
	if(newTextColor.length > 0) obj.textColor = newTextColor;
	if(newBackgroundColor.length > 0) obj.backgroundColor = newBackgroundColor;
	console.log(obj);
	$.ajax({
		url: 'updateClient',
		type: 'get',
		data: {obj: obj},
		success: function(response){
			displayNotification(response);
			location.reload();
		}
	});
}


/* fading out errors and flashes */
if($('#errors').is(':visible') == true) {
	setTimeout(function(){
		$('#errors').fadeOut();
	}, 4000);
}

if($('#flash-message').is(':visible') == true) {
	setTimeout(function(){
		$('#flash-message').fadeOut();
	}, 2500);
}

/* Hiding and viewing new client form */
$('#clientadd').on('click', function(){
	if($('#newclient').is(':visible') == true) {
		$('#newclient').fadeOut(200, function(){
			$('#clientadd').removeClass();
			$('#clientadd').addClass('fa fa-plus-circle fa-2x');
		});
	}else{
		$('#newclient').fadeIn(200, function(){
			$('#clientadd').removeClass();
			$('#clientadd').addClass('fa fa-minus-circle fa-2x');
		});
	}
});
