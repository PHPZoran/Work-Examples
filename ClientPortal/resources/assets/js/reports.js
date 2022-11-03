// init highchart
var Highcharts = require('highcharts')

var detailData = {}

var moment = require('moment')
var format = require('moment/src/lib/format/format')

// introduce an array which will contain names of initialised dropdown - prevents duplicate options
var initialised_dropdowns = []

// introduce a var that holds the id of currently opened appointment (detail view)
var currently_opened_appointment = null

// introduce the list of dropdown that appear on appointment detail view
var dropdowns_list = [
  'second_appointment_c',
  'positive_appointment_c',
  'dm_qualified_c',
  'appointment_result_c',
  'opportunity_amount'
]

// introduce the report delivery configuration
// (that's how we're keeping track on if configuration is being edited)
var delivery_report_configuration = {id: null}

// add listener for campaign change
$('#campaigns').on('change', function (event) {

  // reload activities
  load_activities($(this).val(), $('#salesreps').val(), $(this).attr('data-from'))
})

// add listener for salesreps change
$('#salesreps').on('change', function (event) {

  // reload activities
  load_activities($('#campaigns').val(), $(this).val(), $(this).attr('data-from'))
})

// add listener for campaign type change
$('#campaign_type').on('change', function (event) {

  // reload campaigns
  load_campaigns($(this).val())
})

// add listener for appointments filter change
$('#date_created_filter, #appointment_date_filter').on('change', function (event) {

  $('#appointmentsLoader').show()
  $('#appointmentsSection').hide()

  // reload activities
  load_activities()

  // hide loader
  $('#appointmentsLoader').hide()
  $('#appointmentsSection').show()
})

// add listener for downloading xlsx
$('.ms-export-excel').on('click', function (event) {

  // call backend
  downloadAppointments()
})

// add listener on activities dropdown
// (in activity details modal)
$('#et_activities_select').on('change', function (event) {

  // show selected activity
  showActivitySliceInfo($(event.target).val())
})

// add listener on positive appointments dropdown
// (in activity details modal)
$('#et_positive_appointments_select').on('change', function (event) {

  // show selected appointment
  showPositiveAppointmentSliceInfo($(event.target).val())
})

// add listener on second appointment dropdown
// (in activity details modal)
$('#et_second_appointment_select').on('change', function (event) {

  // show selected activity
  showSecondAppointmentSliceInfo($(event.target).val())
})

// add listener on held appointments dropdown
// (in activity details modal)
$('#et_held_appointments_select').on('change', function (event) {

  // show selected activity
  showHeldAppointmentsSliceInfo($(event.target).val())
})

// add listener on max value dropdown
// (in activity details modal)
$('#et_max_value_appointments_select').on('change', function (event) {

  // show selected activity
  showMaxValueAppointmentsSliceInfo($(event.target).val())
})

// add listener on total appointments dropdown
// (in total appointments details modal)
$('#et_total_appointments_select').on('change', function (event) {

  // show appointments in selected status
  showAppointmentsInStatus($(event.target).val())
})

// add listener on 'edit appointment' button
$('#ms_edit_appointment').on('click', function (event) {

  // trigger editing appointment
  edit_appointment()
})

// add listener on 'cancel editing appointment' button
$('#ms_cancel_saving_appointment').on('click', function (event) {

  // trigger cancellation
  cancelSaving()
})

// trigger edited saving appointment
$('#ms_save_appointment').on('click', function (event) {

  // trigger saving app.
  saveEditedAppointment()
})

// add listener on 'save delivery configuration' button
$('#save_delivery_configuration').on('click', function (event) {

  // save delivery configuration
  save_delivery_configuration(event)
})

// add listener on 'send report' button
$('#send_snapshot_report').on('click', function (event) {

  // trigger sending report
  send_report()
})

// add listener on 'hide/show filters' button
$('#ms_toggle_filters').on('click', function (event) {

  // prevent default behaviour
  event.preventDefault()

  // toggle filters
  toggle_filters()
})

// add listener on all delivery configuration fields
$('.ms-delivery-configuration').on('change', function () {

  // handle changing the configuration
  deliveryConfigurationChanged()
})

// add listener on all INPUT delivery configuration fields
$('.ms-delivery-configuration').keyup(function () {

  // handle changing the configuration
  deliveryConfigurationChanged()
})

// add listener on cancel saving configuration
$('#cancel_save_configuration').on('click', function (event) {

  // load configuration once again (so old values are rendered)
  load_report_delivery_configuration()
})

// make sure that this is report page
if (window.location.pathname == '/report') {

  // load all activities
  load_activities()

  // load report delivery configuration
  load_report_delivery_configuration()
}

/**
 * Fetches Campaigns and updates activities based on filter dropdown
 *
 * @param type
 */
function load_campaigns (type) {

  // set header
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  // make ajax call
  $.ajax({
    type: 'GET',
    url: '/report',
    data: {
      type: type
    },
    dataType: 'json',
    success: function (data) {

      // unset campaign results
      $('#campaigns').children('option').remove()

      // go through each campaigns
      $.each(data.campaigns, function (key, value) {

        // set new list pg campaigns
        $('#campaigns')
          .append($('<option selected></option>')
            .attr('value', value.id)
            .text(value.name))
      })

      // unset salesreps
      $('#salesreps').children('option').remove()

      // go through each salesreps
      $.each(data.salesreps, function (key, value) {

        // set new list of salesreps
        $('#salesreps')
          .append($('<option selected></option>')
            .attr('value', value.id)
            .text(value.last_name + ', ' + value.first_name))
      })

      // load activities
      load_activities()
    },
    error: function (data) {

      // display error in console
      console.log('Error:', data.responseText)
    }
  })
}

/**
 * Loads activity data
 *
 * @param campaign_ids
 * @param salesreps_ids
 * @param from
 */
function load_activities (campaign_ids, salesreps_ids, from) {

  if (typeof campaign_ids == 'undefined') {
    campaign_ids = $('#campaigns').val()
    campaign_ids = (typeof campaign_ids == 'undefined') ? [] : campaign_ids
  }
  if (typeof salesreps_ids == 'undefined') {
    salesreps_ids = $('#salesreps').val()
    salesreps_ids = (typeof salesreps_ids == 'undefined') ? [] : salesreps_ids
  }

  // load salesreps
  if (from == 'campaigns') {
    loadSalesreps(campaign_ids)
    // todo: show campaign loaders
    $('#prospectingSection').hide()
    $('#prospectingLoader').show()
    $('#opportunityLoader').show()
    $('#opportunitySection').hide()
    $('#apointmentLoader').show()
    $('#apointmentSection').hide()
  }
  else {
    // TODO: show salesrep loaders
    $('#snapshotLoader').show()
    $('#snapshotSection').hide()
    $('#totalActivityLoader').show()
    $('#totalActivitySection').hide()
  }

  $('#appointmentsLoader').hide()
  $('#appointmentsSection').show()

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/activities',
    data: {
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      date_created: $('#date_created_filter').val(),
      appointment_date: $('#appointment_date_filter').val()
    },
    dataType: 'json',
    success: function (data) {

      setPositiveAppointments()
      setAppointmentsView(data.appointments_results.table_data, data.appointments_results.totals, data.appointments_results.detail_data)
      setLineChart(data.campaign_results.chart_data)
      setAppointmentsMaxValue(data.campaign_results.max_value_pie_chart_data, data.campaign_results.max_value_bar_chart_data)
      setLcTableData(data.campaign_results)
      setBarChart(data.as_ta.barchar)
      setBarChartTable(data.as_ta)
      setPieChart(data.pie_data)
      setPieAdditionalData(data.table_data)
      setAppointmentSnapshoot(data.as_ta)

      // initialises dropdowns on appointment detail view
      setAppointmentDropDowns(data.dropdowns)

      $('.detail-view').on('click', function (event) {

        event.preventDefault()

        openDetailView(this)

      })
    },
    error: function (data) {

      console.log('Error:', data.responseText)
    }
  })
}

/**
 * Loads SakesReps for selected Campaigns
 *
 * @param campaign_ids
 */
function loadSalesreps (campaign_ids) {
  if (typeof campaign_ids == 'undefined') {
    campaign_ids = $('#campaigns').val()
    campaign_ids = (typeof campaign_ids == 'undefined') ? [] : campaign_ids
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/salesreps',
    data: {
      campaign_ids: campaign_ids
    },
    dataType: 'json',
    success: function (data) {
      $('#salesreps')
        .find('option')
        .remove()
        .end()

      $.each(data, function (key, value) {
        $('#salesreps').append('<option value="' + value.id + '" selected>' + value.last_name + ', ' + value.first_name + '</option>')
      })

      $('#salesreps').trigger('change')
    },
    error: function (data) {
      console.log('Error:', data.responseText)
    }
  })
}

/**
 * Loads Pie chart for Prospecting activity section
 *
 * @param data
 */
function setPieChart (data) {

  $('#prospectingSection').show()
  $('#prospectingLoader').hide()

  Highcharts.chart('ChartCalls', {

    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    tooltip: {
      pointFormat: '<b>{point.y}</b>'
    },
    title: {
      text: ''
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: '<b>{point.name}</b>',
          style: {
            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
          }
        },
        events: {
          click: function (event) {

            // open modal with additional data (related to selected pie slice)
            showActivitySliceInfo(event.point.name, data)
          }
        }
      }
    },
    series: [{
      name: 'Brands',
      colorByPoint: true,
      data: data
    }]
  })
}

/**
 * Opens a modal with additional info about selected slice
 * (contacts title and company information)
 *
 * @param call_outcome [selected slice on activities pie chart]
 * @param chart_data
 */
function showActivitySliceInfo (call_outcome, chart_data) {

  // hide content
  $('#et_activity_details_content').hide()

  // show loader
  $('#et_activity_details_loader').show()

  // show modal
  $('#et_activity_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'prospecting_activity',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      call_outcome: call_outcome
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // make sure that chart data was passed
      // (this happens only when slice is selected on chart;
      // after that 'initialisation', activities dropdown on modal can call this method)
      if (chart_data) {

        // iterate through chart data
        $.each(chart_data, function (index, data) {

          // introduce the piece's name
          var piece_name = data.name

          var option = $('<option></option>')
            .attr('value', piece_name)
            .text(piece_name)

          // check if this piece was clicked on the chart
          if (piece_name == call_outcome) {

            // make this activity (pie chart piece) selected
            option.attr('selected', 'selected')
          }

          // make sure that this dropdown is not already initialised
          if (initialised_dropdowns.indexOf('et_activities_select') === -1) {

            // add pie chart section's name into dropdown
            // (so user can select a different piece to get the details for)
            $('#et_activities_select').append(option)
          }

        })

        // add dropdown to the list of already initialised ones
        initialised_dropdowns.push('et_activities_select')
      }

      // introduce the table element
      var DTable = $('#et_activity_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Contact\'s title'},
          {title: 'Company Name'},
          {title: 'Company City'},
          {title: 'Company State'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_activities_select').val(call_outcome)

      // hide loader
      $('#et_activity_details_loader').hide()

      // show content
      $('#et_activity_details_content').show()

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Sets Pie Chart additional data (beneath piechart)
 *
 * @param table_data
 */
function setPieAdditionalData (table_data) {
  $('#total_activity').text(table_data.total_calls)
  $('#monthly_average').text(Math.ceil(table_data.AvgCalls * 10) / 10)
  $('#current_month').text(table_data.num_month)
  $('#previous_month').text(table_data.num_last)
}

/**
 * Sets Snapshoot table data
 *
 * @param data
 */
function setAppointmentSnapshoot (data) {
  $('#snapshotLoader').hide()
  $('#snapshotSection').show()
  if (typeof data == 'undefined') return
  $('#new_last_week').text(data.new_appointments_last_week)
  $('#attended_last_week').text(data.attended_last_week)
  $('#total_generated').text(data.total_generated)
  $('#total_attended').text(data.total_attended)
  $('#campaign_target').text(data.campaign_target)
}

/**
 * Set barchart data for activities
 *
 * @param data
 */
function setBarChart (data) {
  if (typeof data == 'undefined')
    data = []

  $('#totalActivityLoader').hide()
  $('#totalActivitySection').show()

  Highcharts.chart('ChartAppSet', {
    chart: {
      type: 'column'
    },
    title: {
      text: ''
    },
    xAxis: {
      categories: ['Upcoming', 'Reschedule', 'Attended', 'Cancelled', 'Confirmed'],
      labels: {
        rotation: -45
      }
    },
    yAxis: {
      title: {
        text: ''
      }
    },
    series: data,
    plotOptions: {
      series: {
        cursor: 'pointer',
        point: {
          events: {
            click: function (event) {

              // open modal with additional data (related to selected bar)
              showAppointmentsInStatus(this.category, data)
            }
          }
        }
      }
    },
    legend: {
      enabled: false
    }
  })
}

/**
 * Set bar Chart Table data
 *
 * @param data
 */
function setBarChartTable (data) {

  $('#totalAppointments').text(data.num_appointments)
  $('#totalAccepted').text(data.accepted_total)
  $('#totalReschedules').text(data.rescheduled_total)
  $('#totalAttended').text(data.attended_total)
  $('#totalCanceled').text(data.cancelled_total)
  $('#totalConfirmed').text(data.confirmed_total)
}

/**
 * Sets Line chart for campaign results
 *
 * @param data
 */
function setLineChart (data) {

  $('#opportunityLoader').hide()
  $('#opportunitySection').show()

  Highcharts.chart('ChartMaxPotentialLine', {

    title: {
      text: '',
      x: -20
    },
    subtitle: {
      text: '',
      x: -20
    },
    xAxis: {
      categories: ['Within 6 Months', '6-12 Months', 'Greater than 12 Months']
    },
    yAxis: {
      labels: {
        formatter: function () {
          if (this.value > 1000 && this.value < 1000000) return '$' + Highcharts.numberFormat(this.value / 1000, 0) + 'K'  // maybe only switch if > 1000
          else if (this.value >= 1000000) return '$' + Highcharts.numberFormat(this.value / 1000000, 0) + 'M'  // maybe only switch if > 1000
          return Highcharts.numberFormat(this.value)
        }
      },
      title: {
        text: 'Total Value'
      },
      min: 0,
      lineWidth: 1,
      plotLines: [{
        value: 1,
        width: 1,
        color: '#808080'
      }]
    },
    tooltip: {
      valueSuffix: '',
      pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>${point.y:,.0f}</b><br/>',
    },
    legend: {
      layout: 'vertical',
      align: 'right',
      verticalAlign: 'middle',
      borderWidth: 0
    },
    plotOptions: {
      series: {
        dataLabels: {
          enabled: true,
          format: '${y:,.0f}'
        }
      }
    },
    series: [{
      name: 'Sum Value',
      data: data
    }]

  })
}

/**
 * Sets Estimated Opportunity Value table values
 *
 * @param data
 */
function setLcTableData (data) {
  // remove chart data
  delete data.chart_data
  delete data.max_value_pie_chart_data
  delete data.max_value_bar_chart_data

  // go through data
  for (var key in data) {
    // build part of id
    var id = key.replace(/[^A-Z0-9]/ig, '')

    // set values
    $('#' + id + 'Qty').text(data[key].Qty)
    $('#' + id + 'CumulativeValue').text(formatCurrency(data[key]['Cumulative Value']))
    $('#' + id + 'TotalCount').text(data[key]['Total Count'])
  }
}

/**
 * Helper function for formating numbers as currency (in $)
 *
 * @param x
 * @returns {string}
 */
function formatCurrency (x) {
  return '$ ' + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

/**
 * Sets data for positive appointments
 */
function setPositiveAppointments () {

  // set headers
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/positive-appointments',
    data: {
      campaign_ids: $('#campaigns').val(),
      salesrep_ids: $('#salesreps').val()
    },
    dataType: 'json',
    success: function (data) {

      // hide loader
      $('#apointmentLoader').hide()

      // display content
      $('#apointmentSection').show()

      $('#secondApointmentLoader').hide()
      $('#secondApointmentSection').show()

      $('#apointmentHeldLoader').hide()
      $('#apointmentHeldSection').show()

      // display pie chart
      Highcharts.chart('ChartPositivePie', {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
        },
        title: {
          text: ''
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>'//: {point.percentage:.1f} %'
            },
            events: {
              click: function (event) {

                // open modal with additional data (related to selected pie slice)
                showPositiveAppointmentSliceInfo(event.point.name, data)
              }
            }
          }
        },
        series: [{
          type: 'pie',
          name: 'Activity',
          data: data.positive_pie_chart_data || []
        }]
      })

      // populate pie chart table
      $('#total_held_positive, ' +
        '#total_held_second,' +
        '#total_held_held').text(data.table_data.num_held || 0)
      $('#current_month_positive, ' +
        '#current_month_second,' +
        '#current_month_held').text(data.table_data.num_held_month || 0)

      // display bar chart
      Highcharts.chart('ChartPositiveBar', {
        chart: {
          type: 'bar'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: ['Awaiting Feedback', 'Good Future Prospect', 'Foot in the Door', 'Redirected to Decision Maker', 'Not Worth While'],
        },
        series: [{
          name: 'Opportunity Timeline',
          data: data.positive_bar_chart_data || []
        }],
        legend: {
          enabled: false
        }

      })

      // populate bar chart table
      $('#monthly_average_positive, ' +
        '#monthly_average_second, ' +
        '#monthly_average_held').text(data.table_data.num_held_avg || 0)
      $('#previous_month_positive, ' +
        '#previous_month_second, ' +
        '#previous_month_held').text(data.table_data.num_held_last || 0)

      Highcharts.chart('ChartSecondPie', {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
        },
        title: {
          text: ''
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>'//: {point.percentage:.1f} %'
            },
            events: {
              click: function (event) {

                // open modal with additional data (related to selected pie slice)
                showSecondAppointmentSliceInfo(event.point.name, data)
              }
            }
          }
        },
        series: [{
          type: 'pie',
          name: 'Activity',
          data: data.second_positive_pie_chart_data || []
        }]
      })

      // display bar chart
      Highcharts.chart('ChartSecondBar', {
        chart: {
          type: 'bar'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: ['Awaiting Feedback', 'Yes', 'No']
        },
        series: [{
          name: 'Opportunity Timeline',
          data: data.second_positive_bar_chart_data || []
        }],
        legend: {
          enabled: false
        }

      })

      // appointments held pie chart
      Highcharts.chart('ChartHeldPie', {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false
        },
        title: {
          text: ''
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              format: '<b>{point.name}</b>'//: {point.percentage:.1f} %'
            },
            events: {
              click: function (event) {

                // open modal with additional data (related to selected pie slice)
                showHeldAppointmentsSliceInfo(event.point.name, data)
              }
            }
          }
        },
        series: [{
          type: 'pie',
          name: 'Activity',
          data: data.appointments_held_pie_chart_data || []
        }]
      })

      // appointments held bar chart
      Highcharts.chart('ChartAppHeld', {
        chart: {
          type: 'bar'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: ['Awaiting Timeline', 'Within 6 Months', '6-12 Months', 'Greater than 12 Months'],
        },
        series: [{
          name: 'Opportunity Timeline',
          data: data.appointments_held_bar_chart_data
        }],
        legend: {
          enabled: false
        }
      })
    },
    error: function (data) {

      console.log('Error:', data.responseText)
    }
  })

}

/**
 * Opens a modal with additional info about selected slice
 * (contacts title and company information)
 *
 * @param appointment_outcome [selected slice on positive appointments pie chart]
 * @param chart_data
 */
function showHeldAppointmentsSliceInfo (appointment_outcome, chart_data) {

  // hide content
  $('#et_held_appointments_content').hide()

  // show loader
  $('#et_held_appointments_loader').show()

  // show modal
  $('#et_held_appointments_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'held_appointments',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      appointment_outcome: appointment_outcome
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // make sure that chart data was passed
      // (this happens only when slice is selected on chart;
      // after that 'initialisation', activities dropdown on modal can call this method)
      if (chart_data) {

        // iterate through chart data
        $.each(chart_data.appointments_held_pie_chart_data, function (index, data) {

          // introduce the piece's name
          var piece_name = data.name || data[0]

          var option = $('<option></option>')
            .attr('value', piece_name)
            .text(piece_name)

          // check if this piece was clicked on the chart
          if (piece_name == appointment_outcome) {

            // make this activity (pie chart piece) selected
            option.attr('selected', 'selected')
          }

          // make sure that this dropdown is not already initialised
          if (initialised_dropdowns.indexOf('et_held_appointments_select') === -1) {

            // add pie chart section's name into dropdown
            // (so user can select a different piece to get the details for)
            $('#et_held_appointments_select').append(option)
          }

        })

        // add dropdown to the list of already initialised ones
        initialised_dropdowns.push('et_held_appointments_select')
      }

      // introduce the table element
      var DTable = $('#et_held_appointments_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Meeting Number'},
          {title: 'Contact\'s Name'},
          {title: 'Contact\'s Title'},
          {title: 'Company Name'},
          {title: 'Company State'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_held_appointments_select').val(appointment_outcome)

      // hide loader
      $('#et_held_appointments_loader').hide()

      // show content
      $('#et_held_appointments_content').show()

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Opens a modal with additional info about selected slice
 * (contacts title and company information)
 *
 * @param appointment_outcome [selected slice on positive appointments pie chart]
 * @param chart_data
 */
function showSecondAppointmentSliceInfo (appointment_outcome, chart_data) {

  // hide content
  $('#et_second_appointment_content').hide()

  // show loader
  $('#et_second_appointment_loader').show()

  // show modal
  $('#et_second_appointment_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'second_appointment',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      appointment_outcome: appointment_outcome
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // make sure that chart data was passed
      // (this happens only when slice is selected on chart;
      // after that 'initialisation', activities dropdown on modal can call this method)
      if (chart_data) {

        // iterate through chart data
        $.each(chart_data.second_positive_pie_chart_data, function (index, data) {

          // introduce the piece's name
          var piece_name = data.name || data[0]

          var option = $('<option></option>')
            .attr('value', piece_name)
            .text(piece_name)

          // check if this piece was clicked on the chart
          if (piece_name == appointment_outcome) {

            // make this activity (pie chart piece) selected
            option.attr('selected', 'selected')
          }

          // make sure that this dropdown is not already initialised
          if (initialised_dropdowns.indexOf('et_second_appointment_select') === -1) {

            // add pie chart section's name into dropdown
            // (so user can select a different piece to get the details for)
            $('#et_second_appointment_select').append(option)
          }

        })

        // add dropdown to the list of already initialised ones
        initialised_dropdowns.push('et_second_appointment_select')
      }

      // introduce the table element
      var DTable = $('#et_second_appointment_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Meeting Number'},
          {title: 'Contact\'s Name'},
          {title: 'Contact\'s Title'},
          {title: 'Company Name'},
          {title: 'Company State'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_second_appointment_select').val(appointment_outcome)

      // hide loader
      $('#et_second_appointment_loader').hide()

      // show content
      $('#et_second_appointment_content').show()

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Opens a modal with additional info about selected slice
 * (contacts title and company information)
 *
 * @param appointment_outcome [selected slice on positive appointments pie chart]
 * @param chart_data
 */
function showPositiveAppointmentSliceInfo (appointment_outcome, chart_data) {

  // hide content
  $('#et_positive_appointments_content').hide()

  // show loader
  $('#et_positive_appointments_loader').show()

  // show modal
  $('#et_positive_appointments_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'positive_appointment',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      appointment_outcome: appointment_outcome
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // make sure that chart data was passed
      // (this happens only when slice is selected on chart;
      // after that 'initialisation', activities dropdown on modal can call this method)
      if (chart_data) {

        // iterate through chart data
        $.each(chart_data.positive_pie_chart_data, function (index, data) {

          // introduce the piece's name
          var piece_name = data.name || data[0]

          var option = $('<option></option>')
            .attr('value', piece_name)
            .text(piece_name)

          // check if this piece was clicked on the chart
          if (piece_name == appointment_outcome) {

            // make this activity (pie chart piece) selected
            option.attr('selected', 'selected')
          }

          // make sure that this dropdown is not already initialised
          if (initialised_dropdowns.indexOf('et_positive_appointments_select') === -1) {

            // add pie chart section's name into dropdown
            // (so user can select a different piece to get the details for)
            $('#et_positive_appointments_select').append(option)
          }

        })

        // add dropdown to the list of already initialised ones
        initialised_dropdowns.push('et_positive_appointments_select')
      }

      // introduce the table element
      var DTable = $('#et_positive_appointments_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Meeting Number'},
          {title: 'Contact\'s Name'},
          {title: 'Contact\'s Title'},
          {title: 'Company Name'},
          {title: 'Company State'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_positive_appointments_select').val(appointment_outcome)

      // hide loader
      $('#et_positive_appointments_loader').hide()

      // show content
      $('#et_positive_appointments_content').show()

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Opens a modal with additional info about selected slice
 * (contacts title and company information)
 *
 * @param value_range [selected slice on positive appointments pie chart]
 * @param chart_data
 */
function showMaxValueAppointmentsSliceInfo (value_range, chart_data) {

  // hide content
  $('#et_max_value_appointments_content').hide()

  // show loader
  $('#et_max_value_appointments_loader').show()

  // show modal
  $('#et_max_value_appointments_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'max_value',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      value_range: value_range
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // make sure that chart data was passed
      // (this happens only when slice is selected on chart;
      // after that 'initialisation', activities dropdown on modal can call this method)
      if (chart_data) {

        // iterate through chart data
        $.each(chart_data, function (index, data) {

          // introduce the piece's name
          var piece_name = data.name || data[0]

          var option = $('<option></option>')
            .attr('value', piece_name)
            .text(piece_name)

          // check if this piece was clicked on the chart
          if (piece_name == value_range) {

            // make this activity (pie chart piece) selected
            option.attr('selected', 'selected')
          }

          // make sure that this dropdown is not already initialised
          if (initialised_dropdowns.indexOf('et_max_value_appointments_select') === -1) {

            // add pie chart section's name into dropdown
            // (so user can select a different piece to get the details for)
            $('#et_max_value_appointments_select').append(option)
          }

        })

        // add dropdown to the list of already initialised ones
        initialised_dropdowns.push('et_max_value_appointments_select')
      }

      // introduce the table element
      var DTable = $('#et_max_value_appointments_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Meeting Number'},
          {title: 'Contact\'s Name'},
          {title: 'Contact\'s Title'},
          {title: 'Company Name'},
          {title: 'Company State'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_max_value_appointments_select').val(value_range)

      // hide loader
      $('#et_max_value_appointments_loader').hide()

      // show content
      $('#et_max_value_appointments_content').show()

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Opens a modal with additional info about selected appointment category
 * (total appointments bar chart)
 *
 * @param status (of appointment)
 * @param chart_data
 */
function showAppointmentsInStatus (status, chart_data) {

  // hide content
  $('#et_total_appointments_content').hide()

  // show loader
  $('#et_total_appointments_loader').show()

  // show modal
  $('#et_total_appointments_details').modal('show')

  // retrieve selected campaign
  var campaign_ids = $('#campaigns').val()

  // retrieve selected salesreps
  var salesreps_ids = $('#salesreps').val()

  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
  })

  $.ajax({
    type: 'POST',
    url: '/chart-drill-down',
    data: {
      action: 'total_appointments',
      campaign_ids: campaign_ids,
      salesreps_ids: salesreps_ids,
      status: status
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (!response.success) {

        // todo: show failure message

        return
      }

      // introduce the table element
      var DTable = $('#et_total_appointments_details_table')

      // destroy previous data-table so new one could be initialised
      if (DTable.hasClass('dataTable'))
        DTable.DataTable().destroy()

      // introduce the data array
      // (it was formatted in the back-end)
      var data = response.data

      // initialise data-table
      DTable.DataTable({
        data: data,
        columns: [
          {title: 'Meeting Number'},
          {title: 'Meeting Date'},
          {title: 'Contact Name'},
          {title: 'Account Name'},
          {title: 'Client Sales Rep'}
        ]
      })

      // set the selection in 'filter' dropdown
      $('#et_total_appointments_select').val(status)

      // hide loader
      $('#et_total_appointments_loader').hide()

      // show content
      $('#et_total_appointments_content').show()

      // attach listener on appointment details link
      $('.detail-view').on('click', function (event) {

        event.preventDefault()

        openDetailView(this)
      })

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Sets charts and table data for AppointmentsMaxValue
 *
 * @param pie_data
 * @param bar_data
 */
function setAppointmentsMaxValue (pie_data, bar_data) {

  $('#apointmentMaxValueLoader').hide()
  $('#apointmentMaxValueSection').show()

  var mapping = {
    '0-35k': '0_35k',
    '35k - 75k': '35_75k',
    '75k-150k': '75_150k',
    '150k - 400k': '150_400k',
    '400k - 1m': '400_1m',
    '1m +': '1m_plus'
  }

  var labelmaps = {
    '0-35k': '$0 - $35k',
    '35k - 75k': '$35k - $75k',
    '75k-150k': '$75k - $150k',
    '150k - 400k': '$150k - $400k',
    '400k - 1m': '$400k - $1M',
    '1m +': '$1M +'
  }

  var lblpiedata = []

  $.each(pie_data, function (key, value) {

    // make sure that value is greater than 0
    if (value[1] > 0) {

      lblpiedata.push([labelmaps[value[0]], value[1]])
    }
  })

  // set table data!
  $.each(pie_data, function (key, value) {
    $('#' + mapping[value[0]]).text(value[1])
    $('#' + mapping[value[0]] + '_bar').text(value[1])
  })

  Highcharts.chart('appointmentsMaxValuePie', {
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false
    },
    title: {
      text: ''
    },
    tooltip: {
      pointFormat: '<b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true
        },
        showInLegend: true,
        events: {
          click: function (event) {

            // open modal with additional data (related to selected pie slice)
            showMaxValueAppointmentsSliceInfo(event.point.name, lblpiedata)
          }
        }
      }
    },
    series: [{
      type: 'pie',
      name: '',
      data: lblpiedata
    }]
  })

  Highcharts.chart('appointmentsMaxValueBar', {
    chart: {
      type: 'bar'
    },
    title: {
      text: ''
    },
    subtitle: {
      text: ''
    },
    xAxis: {
      categories: ['$0-$35k', '$35k-$75k', '$75k-$150k', '$150k-$400k', '$400k-$1M', '$1M +'],
      title: {
        text: null
      }
    },
    yAxis: {
      min: 0,
      title: {
        text: 'Count',
        align: 'high'
      }
    },
    tooltip: {
      valueSuffix: ''
    },
    plotOptions: {
      bar: {
        dataLabels: {
          enabled: true
        }
      }
    },
    legend: {
      enabled: false,
      layout: 'vertical',
      align: 'right',
      verticalAlign: 'top',
      x: -40,
      y: 100,
      floating: true,
      borderWidth: 1,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor || '#FFFFFF'),
      shadow: true
    },
    credits: {
      enabled: false
    },
    series: [{
      name: 'Opportunities',
      data: bar_data
    }]
  })
}

/**
 * Initialises dropdowns on appointment detail view
 *
 * @param dropdowns
 */
function setAppointmentDropDowns (dropdowns) {

  // iterate through retrieved dropdowns
  $.each(dropdowns, function (field, dropdown_options) {

    // iterate through dropdown options
    $.each(dropdown_options, function (index, key_value_pair) {

      // set the option in dropdown
      // (hint: this complication with setting value and label
      // is caused by request to preserve order in which dropdown options are shown.
      // that's why each value/label pair is an object)
      $('#' + field).append($('<option>')
        .val(Object.keys(key_value_pair)[0])
        .html(key_value_pair[Object.keys(key_value_pair)]
        )
      )

    })

  })

}

/**
 * Populates data for appointments view
 *
 * @param data
 * @param totals
 * @param detail_data
 */
function setAppointmentsView (data, totals, detail_data) {

  detailData = detail_data
  var DTable = $('#appointment_list_view')

  if (DTable.hasClass('dataTable'))
    DTable.DataTable().destroy()

  $.fn.dataTable.moment('MM-DD-YYYY hh:mm A')

  var a = DTable.DataTable({
    data: data,
    columns: [
      {title: 'Number'},
      {title: 'Campaign Name'},
      {title: 'Status'},
      {title: 'Format'},
      {title: 'Date', type: 'date'},
      {title: 'Account'},
      {title: 'Title'},
      {title: 'Contact Name'},
      {title: 'Sales Rep'},
      {title: 'Opportunity Timeline'},
      {title: 'Opportunity Amount'}
    ],
    order: [[4, 'desc']],
    rowCallback: function (row, row_data, index) {

      // introduce the appointment's date entered
      var appointment_date = row_data[11]

      // introduce the appointment date object
      var appointment_date_object = moment(appointment_date, 'MM-DD-YYYY')

      // calculate the interval that passed since the meeting was scheduled (in days)
      var interval = moment().diff(appointment_date_object, 'days')

      // make sure that appointment was scheduled in the last 7 days
      if (interval <= 7) {

        // highlight the row
        $(row).css('background-color', '#F2D1D1')

      }

    }
  })

  DTable.on('draw.dt', function () {
    setTimeout(function () {
      $('.detail-view').on('click', function (event) {

        event.preventDefault()

        openDetailView(this)

      })
    }, 300)
  })

  // populate data
  $.each(totals, function (key, value) {

    $('#' + key).text(value)
  })
}

/**
 * Downloads xlsx filre that is a representation of appointments displayed on the view
 */
function downloadAppointments () {
  // set headers
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
  })

  $.ajax({
    type: 'POST',
    url: '/download-appointments',
    data: {
      campaign_ids: $('#campaigns').val(),
      salesreps_ids: $('#salesreps').val(),
      date_created: $('#date_created_filter').val(),
      appointment_date: $('#appointment_date_filter').val()
    },
    dataType: 'json',
    success: function (data) {

      // download attachment
      var link = document.createElement('a')
      link.href = data
      link.click()
    },
    error: function (data) {

      // log error
      console.log('Error:', data.responseText)
    }
  })
}

/**
 * Opens appointment detail view
 *
 * @param element
 * @param element_id
 */
function openDetailView (element, element_id) {

  // introduce the sugar id of app. that was clicked on
  var id = element_id || $(element).attr('id')

  // set the var
  currently_opened_appointment = id

  // make sure that app. is in the apps. array
  if (detailData[id]) {

    // iterate through all the appointment data
    $.each(detailData[id], function (field_name, field_value) {

      // check if field is actually a dropdown
      if ($.inArray(field_name, dropdowns_list) !== -1) {

        // set dropdown value
        $('#' + field_name).val(field_value)

      } else {

        // add value to the field on modal
        $('#' + field_name).html(field_value)
      }
    })

    // hide save button
    $('#ms_save_appointment').hide()

    // hide cancel saving button
    $('#ms_cancel_saving_appointment').hide()

    // show edit button
    $('#ms_edit_appointment').show()

    // disable editing editable fields
    $('.ms-editable').prop('disabled', 'disabled')

    // show modal
    $('.app-modal').modal('show')
  }
}

/**
 * Loads shapshot report configuration
 * (email which report should be sent to and time when that should happen)
 */
function load_report_delivery_configuration () {

  // show loader
  $('#reportDeliveryLoader').show()

  // hide the content
  $('#reportDeliverySection').hide()

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/report-delivery',
    data: {
      load_configuration: true
    },
    dataType: 'json',
    success: function (response) {

      // hide loader
      $('#reportDeliveryLoader').hide()

      // show the content
      $('#reportDeliverySection').show()

      // clear the fields from previous values
      $('#et_email_address').val('')
      $('#et_email_address_cc').val('')
      $('#et_send_at_day').val('1')
      $('#et_send_at_time').val('01:00')
      $('#et_send_at_period').val('AM')

      // show 'send report' button
      $('#send_snapshot_report').show()

      // hide save button
      $('#save_delivery_configuration').hide()

      // hide cancel button
      $('#cancel_save_configuration').hide()

      // make sure that call was successful
      if (response.success) {

        // set global var
        delivery_report_configuration = response.data

        // set the data in their fields
        $('#et_email_address').val(delivery_report_configuration.email_address)
        $('#et_email_address_cc').val(delivery_report_configuration.email_address_cc)
        $('#et_send_at_day').val(delivery_report_configuration.send_at_day)
        $('#et_send_at_time').val(delivery_report_configuration.send_at_time)
        $('#et_send_at_period').val(delivery_report_configuration.send_at_period)

      } else {

        // make sure that actual error occurred
        // (if user doesn't have configuration set yet that information will be retrieved as error)
        if (response.message != 'no-configuration') {

          // show error message
          showMessage('An error occurred', response.message)
        }

      }

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Saves delivery configuration
 *
 * @param event
 */
function save_delivery_configuration (event) {

  // introduce the report delivery 'configuration'
  var data = retrieve_delivery_configuration()

  // validate entries
  if (!data) {
    return
  }

  // show loader
  $('#reportDeliveryLoader').show()

  // hide the content
  $('#reportDeliverySection').hide()

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/report-delivery',
    data: {
      save_configuration: true,
      data: data
    },
    dataType: 'json',
    success: function (response) {

      // hide loader
      $('#reportDeliveryLoader').hide()

      // show the content
      $('#reportDeliverySection').show()

      // make sure that call was successful
      if (response.success) {

        // show success message
        showMessage('Success', 'Report delivery configuration has been saved')

        // save config id
        delivery_report_configuration.id = response.configuration_id

        // show 'send report' button
        $('#send_snapshot_report').show()

        // hide save button
        $('#save_delivery_configuration').hide()

        // hide cancel button
        $('#cancel_save_configuration').hide()

      } else {

        // show error message
        showMessage('An error occurred. Please contact your administrator', response.message)
      }

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })

}

/**
 * Validates and retrieves delivery configuration
 *
 * @returns {*}
 */
function retrieve_delivery_configuration () {

  // introduce the email address
  var email_address = $('#et_email_address').val().trim()

  // make sure that email address is entered
  if (!email_address) {

    // show error message
    showMessage('Email missing', 'Please enter an email address before saving configuration')

    return false
  }

  // introduce the email regex
  var email_regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

  var email_array = email_address.split(';')

  email_array.forEach(function (eml) {
    // make sure that email is valid
    if (!email_regex.test(eml)) {
      // show error message
      showMessage('Email invalid', 'Please enter a valid email address before saving configuration')
      return false
    }
  })
  // introduce the CC email address
  var email_address_cc = $('#et_email_address_cc').val().trim()

  // make sure that all 'time' fields are populated
  if (!$('#et_send_at_day').val() || !$('#et_send_at_time').val() || !$('#et_send_at_period').val()) {

    // show error message
    showMessage('Email invalid', 'Please populate all "time" fields before saving configuration')

    return false
  }

  // make sure that CC email address is entered
  if (email_address_cc) {

    var cc_array = email_address_cc.split(';')

    cc_array.forEach(function (cml) {
      // make sure that email is valid
      if (!email_regex.test(cml)) {

        // show error message
        showMessage('Email invalid', 'Please enter a valid CC email address before saving configuration')

        return false
      }
    })

  }

  // introduce the config's id before it's overwritten
  var id = delivery_report_configuration.id

  // return the configuration
  delivery_report_configuration = {
    email_address: email_address,
    email_address_cc: email_address_cc,
    send_at_day: $('#et_send_at_day').val(),
    send_at_time: $('#et_send_at_time').val(),
    send_at_period: $('#et_send_at_period').val(),
    id: id
  }

  return delivery_report_configuration
}

/**
 * Shows error in modal
 *
 * @param title
 * @param message
 */
function showMessage (title, message) {

  var modal = $('#reportDeliveryModal')

  modal.find('.error-heading').text(title)

  modal.find('.error-modal-body').text(message)

  modal.modal('show')
}

/**
 * Triggers sending report
 */
function send_report () {

  // show loader
  $('#reportDeliveryLoader').show()

  // hide the content
  $('#reportDeliverySection').hide()

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  $.ajax({
    type: 'POST',
    url: '/report-delivery',
    data: {
      send_report: true
    },
    dataType: 'json',
    success: function (response) {

      // hide loader
      $('#reportDeliveryLoader').hide()

      // show the content
      $('#reportDeliverySection').show()

      // make sure that call was successful
      if (response.success) {

        showMessage('Success', 'Report snapshot has been sent to set email address')

      } else {

        // show error message
        showMessage('An error occurred. Please contact your administrator', response.message)
      }

    },
    error: function (response) {

      console.log('Error:', response.responseText)
    }
  })
}

/**
 * Triggers editing appointment
 * (shows editable fields)
 */
function edit_appointment () {

  // enable dropdowns selection
  $('.ms-editable').removeAttr('disabled')

  // hide edit button
  $('#ms_edit_appointment').hide()

  // show save button
  $('#ms_save_appointment').show()

  // show cancel saving button
  $('#ms_cancel_saving_appointment').show()
}

/**
 * Triggers cancelling of editing appointment
 */
function cancelSaving () {

  // re-open the detail view
  openDetailView(null, currently_opened_appointment)
}

/**
 * Triggers saving edited appointment
 * (hit: certain fields are editable)
 */
function saveEditedAppointment () {

  // retrieve values from editable fields
  var data = {
    appointment_result_c: $('#appointment_result_c').val(),
    opportunity_amount: $('#opportunity_amount').val(),
    second_appointment_c: $('#second_appointment_c').val(),
    positive_appointment_c: $('#positive_appointment_c').val(),
    dm_qualified_c: $('#dm_qualified_c').val() || ''
  }

  // set header
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  })

  showModal('Appointment feedback is being updated, please wait...', '', true)

  // make ajax call
  $.ajax({
    type: 'POST',
    url: '/edit-appointment',
    data: {
      data: data,
      appointment_id: currently_opened_appointment
    },
    dataType: 'json',
    success: function (response) {

      // make sure that call was successful
      if (response.success) {

        // show success message
        showModal('Success', 'Appointment feedback has been successfully updated.', false)

        if (!response.appointment) {

          // show success message
          showModal('An error occurred', 'Appointment was not updated. Please refresh the page and try again.', false)

          return
        }

        // set updated information into global var
        detailData[currently_opened_appointment]['second_appointment_c'] = response.appointment['second_appointment_c']
        detailData[currently_opened_appointment]['positive_appointment_c'] = response.appointment['positive_appointment_c']
        detailData[currently_opened_appointment]['dm_qualified_c'] = response.appointment['dm_qualified_c']
        detailData[currently_opened_appointment]['appointment_result_c'] = response.appointment['appointment_result_c']
        detailData[currently_opened_appointment]['opportunity_amount'] = response.appointment['opportunity_amount']

        // re-open the detail view (updated fields will be shown)
        openDetailView(null, currently_opened_appointment)

      } else {

        // show success message
        showModal('An error occurred. Please contact your administrator.', response.message, false)
      }

    },
    error: function (error) {

      // display error in console
      console.log('Error:', error.responseText)
    }
  })

}

/**
 * Displays message (or loader in modal)
 *
 * @param title
 * @param content
 * @param loader
 */
function showModal (title, content, loader) {

  var modal = $('#ms_universal_modal')

  modal.find('.ms-title').html(title)

  var options = {}

  if (loader) {

    modal.find('.ms-body').html('')
    modal.find('.ms-loader').show()
    modal.find('.ms-footer').hide()

    // prevent modal from hiding when it's clicked outside of it
    options = {
      backdrop: 'static',
      keyboard: false
    }

    // kill previous modal
    modal.data('bs.modal', null)

  } else {

    modal.find('.ms-body').html(content)
    modal.find('.ms-loader').hide()
    modal.find('.ms-footer').show()
  }

  // show modal
  modal.modal(options)
}

/**
 * Handles changing delivery configuration parms
 */
function deliveryConfigurationChanged () {

  // hide 'send report' button
  $('#send_snapshot_report').hide()

  // show save button
  $('#save_delivery_configuration').show()

  // show cancel button
  $('#cancel_save_configuration').show()
}

/**
 * Toggles filters section
 */
function toggle_filters () {

  if (!$('#ms_filters_area').is(':hidden')) {

    $('#ms_filters_area').hide(150)

    $('#ms_toggle_filters').html('Show filters')

  } else {

    $('#ms_filters_area').show(150)

    $('#ms_toggle_filters').html('Hide filters')

  }

}


