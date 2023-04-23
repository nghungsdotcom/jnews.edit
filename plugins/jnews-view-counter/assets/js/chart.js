;(function () {
  'use strict'
  window.jnews = window.jnews || {}
  window.jnews.viewCounter = window.jnews.viewCounter || {}
  window.jnews.viewCounter.chart = function () {
    var base = this,
      globalMonth,
      globalYear,
      defaults = {
        type: 'line',
        data: {
          labels: [],
          datasets: [
            {
              label: '',
              fill: false,
              lineTension: 0,
              borderWidth: 2,
              borderCapStyle: 'butt',
              borderDash: [],
              borderDashOffset: 0.0,
              borderJoinStyle: 'miter',
              pointBorderWidth: 2,
              pointHoverRadius: 4,
              pointHoverBorderWidth: 3,
              pointRadius: 0,
              pointHitRadius: 10,
              data: [],
            },
          ],
        },
        options: {
          chartArea: {
            backgroundColor: '#FAFAFA',
          },
          plugins: {
            tooltip: {
              borderWidth: 1,
              cornerRadius: 0,
              borderColor: 'rgb(238,238,238)',
              backgroundColor: 'rgb(255,255,255)',
              titleColor: '#232323',
              bodyColor: '#838383',
              usePointStyle: true,
              callbacks: {
                title: function (context) {
                  if (context.length) {
                    var label = context[0].label || ''
                    label = moment(context[0].parsed.x).format('MMM DD')
                    return label
                  }
                },
                label: function (context) {
                  var label = context.dataset.label || ''

                  if (label) {
                    label = ' ' + label
                  }
                  if (context.parsed.y !== null) {
                    label = context.parsed.y + label
                  }
                  return label
                },
              },
            },
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                boxWidth: 12,
                boxHeight: 12,
                color: '#686868',
                font: {
                  color: '#686868',
                  size: 12,
                  family: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
                },
                usePointStyle: true,
              },
            },
          },
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'day',
                displayFormats: {
                  day: 'DD[\n]MMM[\n]YYYY',
                },
              },
              display: true,
              grid: {
                display: false,
              },
              ticks: {
                font: {
                  size: 13,
                  weight: 'bold',
                },
                autoSkip: false,
                maxRotation: 0,
                minRotation: 0,
                callback: function (tickValue, index, ticks) {
                  var formatted = tickValue,
                    inRangeMonth = false,
                    inRangeYear = false
                  if (formatted.split('\\n').length > 1) {
                    formatted = formatted.split('\\n')
                    if (undefined === globalMonth) {
                      globalMonth = formatted[1]
                    } else {
                      if (formatted[1] === globalMonth) {
                        inRangeMonth = true
                      }
                      globalMonth = formatted[1]
                    }
                    if (undefined === globalYear) {
                      globalYear = formatted[2]
                    } else {
                      if (formatted[2] === globalYear) {
                        inRangeYear = true
                      }
                      globalYear = formatted[2]
                    }
                    formatted.splice(2, 1)
                    if (inRangeMonth) {
                      if (inRangeYear) {
                        formatted.splice(1, 1)
                      }
                    }
                  }
                  return formatted
                },
              },
            },
            y: {
              display: false,
            },
          },
        },
        plugins: [
          {
            beforeDraw: function (chart, easing) {
              if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
                var ctx = chart.ctx
                var chartArea = chart.chartArea
                ctx.save()
                ctx.fillStyle = chart.config.options.chartArea.backgroundColor
                ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top)
                ctx.restore()
              }
            },
          },
        ],
      },
      chart = null,
      canRender = !!window.CanvasRenderingContext2D,
      element = null,
      cvs = null,
      darkModeToggle,
      HexToRGB = function (hex) {
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i

        hex = hex.replace(shorthandRegex, function (m, r, g, b) {
          return r + r + g + g + b + b
        })

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)

        return result
          ? {
              r: parseInt(result[1], 16),
              g: parseInt(result[2], 16),
              b: parseInt(result[3], 16),
            }
          : null
      }
    base.canRender = function () {
      return canRender
    }
    base.init = function (container, options) {
      if (!base.canRender()) {
        throw new Error('Your browser is too old, JNews cannot create its data chart.')
      }

      if ('undefined' == typeof container) {
        throw new Error('Please tell JNews where to inject the chart.')
      }

      element = container

      if (!element) {
        throw new Error('JNews cannot find ' + container)
      }

      if ('undefined' == typeof Chart) {
        throw new Error('ChartJS library not found')
      }
      darkModeToggle = jnews.library.globalBody.querySelector('.jeg_dark_mode_toggle')
      cvs = document.createElement('canvas')
      element.innerHTML = ''
      element.appendChild(cvs)
    }
    base.isDarkMode = function () {
      if (null !== darkModeToggle) {
        if (darkModeToggle.checked) {
          return true
        }
      }
      return false
    }
    base.populate = function (data) {
      if (chart) {
        chart.destroy()
      }
      var config = JSON.parse(JSON.stringify(defaults))
      // we need to reinsert the function
      config.options.plugins.tooltip.callbacks = defaults.options.plugins.tooltip.callbacks
      config.options.scales.x.ticks.callback = defaults.options.scales.x.ticks.callback
      config.plugins = defaults.plugins

      config.options.scales.x.type = data.x.type
      config.options.scales.x.time = data.x.time
      config.options.scales.y = data.y
      config.data.labels = data.labels
      config.data.datasets[0].label = data.datasets[0].label
      config.data.datasets[0].data = data.datasets[0].data

      if (base.isDarkMode()) {
        data.backgroundColor = '#282828'
        config.options.plugins.legend.labels.color = '#CACACA'
        config.options.plugins.legend.labels.font.color = '#CACACA'
        config.options.scales.x.ticks.color = '#CACACA'
        config.options.scales.x.ticks.font.color = '#CACACA'
        var rgb_tooltip = HexToRGB(data.backgroundColor)
        var rgb_tooltip_border = HexToRGB('#303030')
        config.options.plugins.tooltip.backgroundColor = 'rgba(' + rgb_tooltip.r + ', ' + rgb_tooltip.g + ', ' + rgb_tooltip.b + ',  1)'
        config.options.plugins.tooltip.borderWidth = 1
        config.options.plugins.tooltip.borderColor = 'rgba(' + rgb_tooltip_border.r + ', ' + rgb_tooltip_border.g + ', ' + rgb_tooltip_border.b + ',  1)'
        config.options.plugins.tooltip.titleColor = '#CACACA'
      }
      var rgb_views = HexToRGB(data.color)
      var rgb_chartArea = HexToRGB(data.backgroundColor)
      config.options.chartArea.backgroundColor = 'rgba(' + rgb_chartArea.r + ', ' + rgb_chartArea.g + ', ' + rgb_chartArea.b + ',  1)'
      config.data.datasets[0].backgroundColor = 'rgba(' + rgb_views.r + ', ' + rgb_views.g + ', ' + rgb_views.b + ',  1)'
      config.data.datasets[0].borderColor = data.color
      config.data.datasets[0].pointBorderColor = data.color
      config.data.datasets[0].pointHoverBackgroundColor = data.color
      config.data.datasets[0].pointHoverBorderColor = data.color
      cvs.height = 353
      globalMonth = undefined
      globalYear = undefined
      chart = new Chart(cvs, config)
    }
  }
  window.jnews.viewCounter.chart = new window.jnews.viewCounter.chart()
})()
