import './scss/style.scss'
import Choices from 'choices.js'

(function () {
  function previewEmails () {
    const DOM = {}
    const selectors = {
      container: '#woocommerce-preview-email',
      email: '#choose_email',
      order: '#orderID',
      orderSearch: '#woo_preview_search_orders',
    }

    function cacheDOM () {
      DOM.email = DOM.container.querySelector(selectors.email)
      DOM.order = DOM.container.querySelector(selectors.order)
      DOM.orderSearch = DOM.container.querySelector(selectors.orderSearch)
      DOM.ajaxURL = DOM.container.getAttribute('data-url')
    }

    function searchOrders (query) {
      let controller = new AbortController()
      let signal = controller.signal
      let formData = new FormData()
      formData.append('query', query)
      console.log(DOM.ajaxURL)
      return fetch(DOM.ajaxURL + '?action=woo_preview_orders_search', {
        method: 'POST',
        body: formData,
        signal: signal,
      }).then(response => response.json()).then(data => {
        window.wpe.orderSearch.setChoices(data, 'value', 'label', true)
        return data
      }).catch(function (error) {
        if (error.name === 'AbortError') return
        throw error
      })
    }

    function eventListeners () {
      window.wpe = {}
      window.wpe.emailChoices = new Choices(DOM.email, { removeItemButton: true })
      window.wpe.chooseOrder = new Choices(DOM.order, { removeItemButton: true })
      window.wpe.orderSearch = new Choices(DOM.orderSearch, { removeItemButton: true })

      DOM.orderSearch.addEventListener('search', function (event) {
        // Only search if user has typed 2 or more characters
        if (event.detail.value.length >= 2) {
          searchOrders(event.detail.value)
        }
      })

    }

    function init () {
      DOM.container = document.querySelector(selectors.container)
      if (DOM.container === null) return
      cacheDOM()
      eventListeners()
    }

    init()
  }

  document.addEventListener('DOMContentLoaded', previewEmails)

})()