;(function () {
  'use strict'
  window.jnews = window.jnews || {}
  window.jnews.viewCounter = window.jnews.viewCounter || {}
  window.jnews.viewCounter.general = function () {
    var base = this,
      loadingContent = false,
      xhr = [],
      delay_timer,
      // Bind events
      bindEvent = function () {
        if (darkModeToggle !== null) {
          jnews.library.addEvents(darkModeToggle, {
            change: function () {
              var args = jnews.library.getStorage('view-counter-chart')
              if ('undefined' !== typeof args.range) {
                base.refreshChart('.jnews-view-stats-chart-content', args)
              }
            },
          })
        }
        // if jQuery exist
        if (window.jQuery) {
          // Time Range Option
          if (timeRange.length && timeQuantity.length && timeUnit.length) {
            jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
              toggleCustomRangeWrapper(timeRange.val())
              base.refreshChart('.jnews-view-stats-chart-content', {
                range: timeRange.val(),
                time_quantity: timeQuantity.val(),
                time_unit: timeUnit.val(),
              })
            })
            jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
              timeRange.on('change', function (e) {
                toggleCustomRangeWrapper(this.value)
                base.refreshChart('.jnews-view-stats-chart-content', {
                  range: this.value,
                  time_quantity: timeQuantity.val(),
                  time_unit: timeUnit.val(),
                })
              })
            })
          }

          // Post Nav
          if (postNav.length) {
            jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
              postNav.on('click', function (e) {
                if (!loadingContent) {
                  var ele = jQuery(this),
                    active = postNav.parent().find('li.active')
                  active.removeClass('active')
                  ele.addClass('active')
                  base.refreshContent(ele.data('content'))
                }
              })
            })
          }

          // Custom Time Range Nav
          if (customRangeNav.length) {
            jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
              customRangeNav.on('change', function (e) {
                if (!loadingContent) {
                  var ele = jQuery(this),
                    activeWrapper = jQuery(customRangeWrapper)
                  activeWrapper.find('.form-group.active').removeClass('active')
                  activeWrapper.find('.form-group.' + ele.val()).addClass('active')
                  if ('date-range' === ele.val()) {
                    dates = 1
                    if (timeRange.length && timeQuantity.length && timeUnit.length) {
                      base.refreshChart('.jnews-view-stats-chart-content', {
                        range: timeRange.val(),
                        time_quantity: timeQuantity.val(),
                        time_unit: timeUnit.val(),
                        dates: dates,
                      })
                    }
                  } else {
                    dates = 0
                    if (timeRange.length && timeQuantity.length && timeUnit.length) {
                      base.refreshChart('.jnews-view-stats-chart-content', {
                        range: timeRange.val(),
                        time_quantity: timeQuantity.val(),
                        time_unit: timeUnit.val(),
                        dates: dates,
                      })
                    }
                  }
                  ele.addClass('active')
                }
              })
            })
          }

          // Custom Time Range Submit
          if (timeRange.length && timeQuantity.length && timeUnit.length) {
            jnews.library.forEach(timeQuantity, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                timeQuantity.on('input', function (e) {
                  e.preventDefault()
                  if (!loadingContent) {
                    base.refreshChart('.jnews-view-stats-chart-content', {
                      range: timeRange.val(),
                      time_quantity: timeQuantity.val(),
                      time_unit: timeUnit.val(),
                      dates: dates,
                    })
                  }
                })
              })
            })
            jnews.library.forEach(timeUnit, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                timeUnit.on('change', function (e) {
                  e.preventDefault()
                  if (!loadingContent) {
                    base.refreshChart('.jnews-view-stats-chart-content', {
                      range: timeRange.val(),
                      time_quantity: timeQuantity.val(),
                      time_unit: timeUnit.val(),
                      dates: dates,
                    })
                  }
                })
              })
            })
          }
        }
        // if jQuery not exist
        else {
          // Time Range Option
          if (timeRange.length && timeQuantity.length && timeUnit.length) {
            jnews.library.forEach(timeRange, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                toggleCustomRangeWrapper(ele.value)
                base.refreshChart('.jnews-view-stats-chart-content', {
                  range: ele.value,
                  time_quantity: timeQuantity[0].value,
                  time_unit: timeUnit[0].value,
                })
              })
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                jnews.library.addEvents(ele, {
                  change: function () {
                    toggleCustomRangeWrapper(this.value)
                    base.refreshChart('.jnews-view-stats-chart-content', {
                      range: this.value,
                      time_quantity: timeQuantity[0].value,
                      time_unit: timeUnit[0].value,
                    })
                  },
                })
              })
            })
          }

          // Post Nav
          if (postNav.length) {
            jnews.library.forEach(postNav, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                jnews.library.addEvents(ele, {
                  click: function (e) {
                    if (!loadingContent) {
                      var active = ele.parentNode.querySelector('li.active')
                      jnews.library.removeClass(active, 'active')
                      jnews.library.addClass(this, 'active')
                      base.refreshContent(this.dataset.content)
                    }
                  },
                })
              })
            })
          }

          // Custom Time Range Nav
          if (customRangeNav.length) {
            jnews.library.forEach(customRangeNav, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                jnews.library.addEvents(ele, {
                  change: function (e) {
                    if (!loadingContent) {
                      var active = e.target,
                        activeWrapper
                      jnews.library.forEach(customRangeWrapper, function (ele) {
                        activeWrapper = ele
                        jnews.library.removeClass(activeWrapper.querySelector('.form-group.active'), 'active')
                        jnews.library.addClass(activeWrapper.querySelector('.form-group.' + active.value), 'active')
                        jnews.library.removeClass(active, 'active')
                        if ('date-range' === active.value) {
                          dates = 1
                          if (!loadingContent) {
                            base.refreshChart('.jnews-view-stats-chart-content', {
                              range: timeRange[0].value,
                              time_quantity: timeQuantity[0].value,
                              time_unit: timeUnit[0].value,
                              dates: dates,
                            })
                          }
                        } else {
                          dates = 0
                          if (!loadingContent) {
                            base.refreshChart('.jnews-view-stats-chart-content', {
                              range: timeRange[0].value,
                              time_quantity: timeQuantity[0].value,
                              time_unit: timeUnit[0].value,
                              dates: dates,
                            })
                          }
                        }
                        jnews.library.addClass(active, 'active')
                      })
                    }
                  },
                })
              })
            })
          }

          // Custom Time Range Submit
          if (timeRange.length && timeQuantity.length && timeUnit.length) {
            jnews.library.forEach(timeQuantity, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                jnews.library.addEvents(ele, {
                  input: function (e) {
                    e.preventDefault()
                    if (!loadingContent) {
                      base.refreshChart('.jnews-view-stats-chart-content', {
                        range: timeRange[0].value,
                        time_quantity: timeQuantity[0].value,
                        time_unit: timeUnit[0].value,
                        dates: dates,
                      })
                    }
                  },
                })
              })
            })
            jnews.library.forEach(timeUnit, function (ele, i) {
              jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                jnews.library.addEvents(ele, {
                  change: function (e) {
                    e.preventDefault()
                    if (!loadingContent) {
                      base.refreshChart('.jnews-view-stats-chart-content', {
                        range: timeRange[0].value,
                        time_quantity: timeQuantity[0].value,
                        time_unit: timeUnit[0].value,
                        dates: dates,
                      })
                    }
                  },
                })
              })
            })
          }
        }
      },
      // Apply DateRangePicker
      datePicker = function () {
        if (dateRange.length && window.DateRangePicker) {
          jnews.library.forEach(dateRange, function (ele, i) {
            dateRange = new DateRangePicker(ele, {
              format: 'yyyy-mm-dd',
            })
            var inputRange = dateRange.element.querySelectorAll('input.datepicker-input'),
              setInputRange = function (e) {
                setTimeout(function () {
                  jnews.library.forEach(inputRange, function (ele, i) {
                    if ('date-range-start' === ele.getAttribute('name')) {
                      time_dates = ele.value
                    } else {
                      time_dates += ' ~ ' + ele.value
                    }
                  })
                  if (timeRange.length && timeQuantity.length && timeUnit.length) {
                    base.refreshChart('.jnews-view-stats-chart-content', {
                      range: timeRange[0].value,
                      time_quantity: timeQuantity[0].value,
                      time_unit: timeUnit[0].value,
                      dates: dates,
                    })
                  }
                }, 500)
              }

            jnews.library.forEach(inputRange, function (ele, i) {
              jnews.library.addEvents(ele, {
                input: setInputRange,
                keyup: setInputRange,
                blur: setInputRange,
              })
            })
          })
        }
      },
      toggleCustomRangeWrapper = function (value) {
        var toggleClass = function (ele, value) {
          var is_active = jnews.library.hasClass(ele, 'active')
          if ('custom' === value) {
            if (!is_active) {
              jnews.library.addClass(ele, 'active')
            }
          } else {
            if (is_active) {
              jnews.library.removeClass(ele, 'active')
            }
          }
        }
        jnews.library.forEach(customRangeNavWrapper, function (ele, i) {
          toggleClass(ele, value)
        })
        jnews.library.forEach(customRangeWrapper, function (ele, i) {
          toggleClass(ele, value)
        })
      },
      // DOM
      statsChartWrapper,
      statsContentWrapper,
      dateRange,
      customRangeWrapper,
      customRangeNavWrapper,
      customRangeNav,
      customRangeSubmit,
      timeRange,
      timeQuantity,
      timeUnit,
      postNav,
      darkModeToggle,
      // Value
      dates,
      range,
      time_unit,
      time_quantity,
      time_dates
    base.init = function () {
      statsChartWrapper = jnews.library.globalBody.querySelectorAll('.jnews-view-stats-chart-wrapper')
      statsContentWrapper = jnews.library.globalBody.querySelectorAll('.jnews-view-stats-post-wrapper')
      darkModeToggle = jnews.library.globalBody.querySelector('.jeg_dark_mode_toggle')
      jnews.library.forEach(statsChartWrapper, function (ele, i) {
        customRangeNavWrapper = ele.querySelectorAll('.custom-range-nav-field')
        customRangeWrapper = ele.querySelectorAll('.custom-range-field')
        jnews.library.forEach(customRangeNavWrapper, function (ele, i) {
          if (window.jQuery) {
            ele = jQuery(ele)
            customRangeNav = ele.find('#custom-range-nav')
          } else {
            customRangeNav = ele.querySelectorAll('#custom-range-nav')
          }
        })
        jnews.library.forEach(customRangeWrapper, function (ele, i) {
          dateRange = ele.querySelectorAll('.date-range')
        })
        if (window.jQuery) {
          ele = jQuery(ele)
          timeRange = ele.find('#time-range')
          timeQuantity = ele.find('#time-quantity')
          timeUnit = ele.find('#time-unit')
        } else {
          timeRange = ele.querySelectorAll('#time-range')
          timeQuantity = ele.querySelectorAll('#time-quantity')
          timeUnit = ele.querySelectorAll('#time-unit')
        }
      })
      jnews.library.forEach(statsContentWrapper, function (ele, i) {
        if (window.jQuery) {
          ele = jQuery(ele)
          postNav = ele.find('.jnews-view-stats-post-nav li')
        } else {
          postNav = ele.querySelectorAll('.jnews-view-stats-post-nav li')
        }
      })

      bindEvent()
      datePicker()
    }
    // Refresh stats content
    base.refreshContent = function (content, param) {
      var postContentWrapper = jnews.library.globalBody.querySelector('.jnews-view-stats-post-content-wrapper'),
        postContent = postContentWrapper !== null ? postContentWrapper.querySelector('.jnews-view-stats-post-content') : null,
        overlay = postContentWrapper !== null ? postContentWrapper.querySelector('.module-overlay.stats-post') : null,
        args = {
          action: 'view_counter_stats_template',
          data: {
            nonce: jvcoption.nonce,
            template: content,
            items: content,
            range: range,
            time_quantity: time_quantity,
            time_unit: time_unit,
          },
        }
      if ('undefined' !== typeof param) {
        args.data = jnews.library.extend(args.data, param || {})
        if (param.dates) {
          args.data.dates = time_dates
        }
      }
      if (postContent !== null && overlay !== null) {
        if (content !== postContent.dataset.content || 'undefined' !== typeof param) {
          loadingContent = true
          overlay.style.display = 'block'
          jnews.library.post(jnews_ajax_url, args, function (response) {
            response = JSON.parse(response)
            postContent.innerHTML = response.data.trim()
            if (window.jQuery) {
              jQuery(postContent).find('select').chosen({ disable_search_threshold: 10 })
            }
            overlay.style.display = 'none'
            loadingContent = false
            postContent.dataset.content = content
          })
        }
      }
    }

    // Refresh stats chart
    base.refreshChart = function (selector, value) {
      jnews.library.setStorage('view-counter-chart', value)
      range = value.range
      time_quantity = value.time_quantity
      time_unit = value.time_unit
      var args = {
        action: 'view_counter_chart',
        data: {
          nonce: jvcoption.nonce,
          range: range,
          time_quantity: time_quantity,
          time_unit: time_unit,
        },
      }
      if (value.dates) {
        args.data.dates = time_dates
      }
      clearTimeout(delay_timer)
      delay_timer = setTimeout(function () {
        if (typeof xhr === 'object') {
          xhr.forEach(function (request, i) {
            if ('function' === typeof request.xhr.abort) {
              request.xhr.abort()
            }
          })
          xhr.splice(0, xhr.length)
        }
        xhr.push(
          jnews.library.post(jnews_ajax_url, args, function (response) {
            response = JSON.parse(response)
            var chartWrapper = jnews.library.globalBody.querySelectorAll(selector)
            if (chartWrapper.length) {
              jnews.library.forEach(chartWrapper, function (ele, i) {
                jnews.viewCounter.chart.init(ele)
                jnews.viewCounter.chart.populate(response.data)
              })
            }
            if (window.jQuery) {
              if (postNav.length) {
                jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                  var postContent = postNav.parent().find('li.active').data('content')
                  base.refreshContent(postContent, {
                    range: range,
                    time_quantity: time_quantity,
                    time_unit: time_unit,
                    items: postContent,
                  })
                })
              }
            } else {
              if (postNav.length) {
                jnews.library.forEach(postNav, function (ele, i) {
                  jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
                    var postContent = ele.parentNode.querySelector('li.active').dataset.content
                    base.refreshContent(postContent, {
                      range: range,
                      time_quantity: time_quantity,
                      time_unit: time_unit,
                      items: postContent,
                    })
                  })
                })
              }
            }
          })
        )
      }, 500)
    }
  }
  window.jnews.viewCounter.general = new window.jnews.viewCounter.general()
  if ('object' === typeof jnews && 'object' === typeof jnews.library) {
    jnews.library.requestAnimationFrame.call(jnews.library.win, function () {
      jnews.viewCounter.general.init()
    })
  }
})()
