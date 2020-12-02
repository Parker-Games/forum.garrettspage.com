{$filters}
<div id="{$trackercalendar.id|escape}"></div>
{jq}
	var data = {{$trackercalendar|json_encode}};
	$('#' + data.id).each(function () {
		var cal = this;
		var storeEvent = function(eventInfo) {
			var event = eventInfo.event;
			var request = {
				itemId: event.id,
				trackerId: data.trackerId
			}, end = event.end;

			if (! end) {
				end = event.start;
			}

			request['fields~' + data.begin] = moment(event.start).unix();
			request['fields~' + data.end] = moment(end).unix();
			request['fields~' + data.resource] = event.resourceId;

			$.post($.service('tracker', 'update_item'), request, null, 'json');
		};

		var calendarEl = $(this);
		var calendar = new FullCalendar.Calendar(calendarEl[0], {
			themeSystem: 'bootstrap',
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			eventTimeFormat: {
				hour: 'numeric',
				minute: '2-digit',
				meridiem: data.timeFormat,
				hour12: data.timeFormat,
			},
			slotLabelFormat: {
				hour: 'numeric',
				minute: '2-digit',
				meridiem: data.timeFormat,
				hour12: data.timeFormat
			},
			timeZone: '{{$prefs.display_timezone}}',
			headerToolbar: {
				left: 'prevYear,prev,next,nextYear today',
				center: 'title',
				right: data.views
			},
			editable: true,
			events: $.service('tracker_calendar', 'list', $.extend(data.filterValues, {
				trackerId: data.trackerId,
				colormap: data.colormap,
				beginField: data.begin,
				endField: data.end,
				resourceField: data.resource,
				coloringField: data.coloring,
				filters: data.body,
				maxRecords: data.maxEvents
			})),
			buttonText: {
				resourceTimelineDay: "{tr}resource day{/tr}",
				resourceTimelineMonth: "{tr}resource month{/tr}",
				resourceTimelineYear: "{tr}resource year{/tr}",
				resourceTimelineWeek: "{tr}resource week{/tr}",
				listDay: "{tr}list day{/tr}",
				listMonth: "{tr}list month{/tr}",
				listYear: "{tr}list year{/tr}",
				listWeek: "{tr}list week{/tr}",
				today: "{tr}today{/tr}",
				resourceTimeGridWeek: "{tr}agenda week{/tr}",
				resourceTimeGridDay: "{tr}agenda day{/tr}"
			},
			resources: data.resourceList,
			allDayText: '{tr}all-day{/tr}',
			firstDay: data.firstDayofWeek,
			slotDuration: data.slotDuration,
			slotMinTime: data.minHourOfDay,
			slotMaxTime: data.maxHourOfDay,
			initialView: data.dView,
			eventClick: function(event) {
				if (data.url) {
					var actualURL = data.url;
					actualURL += actualURL.indexOf("?") === -1 ? "?" : "&";

					if (data.trkitemid === "y" && data.addAllFields === "n") {	// "simple" mode
						actualURL += "itemId=" + event.id;
					} else {
						var lOp='';
						var html = $.parseHTML( event.description ) || [];

						// Store useful data values to the URL for Wiki Argument Variable
						// use and to javascript session storage for JQuery use
						actualURL += "trackerid=" + event.trackerId;
						if( data.trkitemid == 'y' ) {
							actualURL = actualURL + "&itemId=" + event.id;
						}
						else {
							actualURL = actualURL + "&itemid=" + event.id;
						}
						actualURL = actualURL + "&title=" + event.title;
						actualURL = actualURL + "&end=" + event.end;
						actualURL = actualURL + "&start=" + event.start;
						if (data.useSessionStorage) {
							sessionStorage.setItem( "trackerid", event.trackerId);
							sessionStorage.setItem( "title", event.title);
							sessionStorage.setItem( "start", event.start);
							sessionStorage.setItem( "itemid", event.id);
							sessionStorage.setItem( "end", event.end);
							sessionStorage.setItem( "eventColor", event.color);
						}

						// Capture the description HTML as variables
						// with the label being the variable name
						$.each( html, function( i, el ) {
							if( isEven( i ) == true ) {
								lOp = el.textContent.replace( ' ', '_' );
							}
							else {
								actualURL = actualURL + "&" + lOp + "=" + el.textContent;
								if (data.useSessionStorage) {
									sessionStorage.setItem( lOp, el.textContent);
								}
							}
						});
					}

					location.href=actualURL;
					return false;

				} else if (event.editable && event.trackerId) {
					var info = {
						trackerId: event.trackerId,
						itemId: event.id
					};
					$.openModal({
						remote: $.service('tracker', 'update_item', info),
						size: "modal-lg",
						title: event.title,
						open: function () {
							$('form:not(.no-ajax)', this)
								.addClass('no-ajax') // Remove default ajax handling, we replace it
								.submit(ajaxSubmitEventHandler(function (data) {
									$(this).parents(".modal").modal("hide")
									$(cal).fullCalendar('refetchEvents');
								}));
						}
					});
					return false;
				} else {
					return true;
				}
			},
			dateClick: function( date, jsEvent, view ) {
				if (data.canInsert) {
					var info = {
						trackerId: data.trackerId
					};
					let momentDate = moment(date.date);
					info[data.beginFieldName] = momentDate.unix();
					info[data.endFieldName] = momentDate.add(1, 'h').unix();
					if (data.url) {
						$('<a href="#"/>').attr('href', data.url);
					} else {
						$.openModal({
							remote: $.service('tracker', 'insert_item', info),
							size: "modal-lg",
							title: data.addTitle,
							open: function () {
								$('form:not(.no-ajax)', this)
									.addClass('no-ajax') // Remove default ajax handling, we replace it
									.submit(ajaxSubmitEventHandler(function (data) {
										$(this).parents(".modal").modal("hide")
										calendar.refetchEvents();
									}));
							}
						});
					}
				}

				return false;
			},
			eventResize: storeEvent,
			eventDrop: storeEvent,
			height: 'auto',
			dayMinWidth: 150, // will cause horizontal scrollbars
		});
		calendar.render();

		$( document ).ready(function() {
			addFullCalendarPrint('#' + data.id, '#calendar-pdf-btn', calendar);
		});
	});
{/jq}
{if $pdf_export eq 'y' and $pdf_warning eq 'n'}
	<a id="calendar-pdf-btn" data-html2canvas-ignore="true"  href="#" style="text-align: right; display: none">{icon name="pdf"} {tr}Export as PDF{/tr}</a>
{/if}
