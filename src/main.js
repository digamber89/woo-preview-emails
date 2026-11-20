import './scss/style.scss'
import Choices from 'choices.js'

/**
 * Utility debounce function to limit execution frequency.
 *
 * @param {Function} func Function to debounce.
 * @param {number} wait Delay in milliseconds.
 * @return {Function} Debounced function.
 */
function debounce (func, wait = 300) {
  let timeout
  return function (...args) {
    clearTimeout(timeout)
    timeout = setTimeout(() => func.apply(this, args), wait)
  }
}

/**
 * Handles email preview form interactions and order search.
 */
function previewEmails () {
  const DOM = {}
  const selectors = {
    container: '#woocommerce-preview-email',
    email: '#choose_email',
    order: '#orderID',
    orderSearch: '#woo_preview_search_orders',
    sendMailTo: '#email',
    clearSendMailTo: '#clearEmail',
  }
  let abortController = null

  function cacheDOM () {
    DOM.container = document.querySelector(selectors.container)
    if (!DOM.container) {
      return false
    }

    DOM.email = DOM.container.querySelector(selectors.email)
    DOM.order = DOM.container.querySelector(selectors.order)
    DOM.orderSearch = DOM.container.querySelector(selectors.orderSearch)
    DOM.sendMailTo = DOM.container.querySelector(selectors.sendMailTo)
    DOM.clearSendMailTo = DOM.container.querySelector(selectors.clearSendMailTo)
    DOM.ajaxURL = DOM.container.getAttribute('data-url') || ''

    return true
  }

  function searchOrders (query) {
    if (abortController) {
      abortController.abort()
    }

    abortController = new AbortController()
    const { signal } = abortController
    const formData = new FormData()
    formData.append('query', query)

    const ajaxUrl = DOM.ajaxURL.includes('?')
      ? `${DOM.ajaxURL}&action=woo_preview_orders_search`
      : `${DOM.ajaxURL}?action=woo_preview_orders_search`

    return fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      signal,
    })
  }

  function eventListeners () {
    window.wpe = window.wpe || {}

    if (DOM.email) {
      window.wpe.emailChoices = new Choices(DOM.email, { removeItemButton: true })
    }

    if (DOM.order) {
      window.wpe.chooseOrder = new Choices(DOM.order, {
        removeItemButton: true,
        shouldSort: false,
      })
    }

    if (DOM.orderSearch) {
      window.wpe.orderSearch = new Choices(DOM.orderSearch, { removeItemButton: true })

      const debouncedSearch = debounce((query) => {
        if (query.length >= 2) {
          searchOrders(query).then((response) => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`)
            }
            return response.json()
          }).then((data) => {
            if (Array.isArray(data) && window.wpe?.orderSearch) {
              window.wpe.orderSearch.setChoices(data, 'value', 'label', true)
            }
            return data
          }).catch((error) => {
            if (error.name === 'AbortError') {
              return
            }
            // eslint-disable-next-line no-console
            console.error('Order search error:', error)
          })
        } else if (window.wpe.orderSearch) {
          window.wpe.orderSearch.clearChoices()
        }
      }, 300)

      DOM.orderSearch.addEventListener('search', (event) => {
        const query = event.detail?.value ? event.detail.value.trim() : ''
        debouncedSearch(query)
      })
    }

    if (DOM.clearSendMailTo && DOM.sendMailTo) {
      DOM.clearSendMailTo.addEventListener('click', () => {
        DOM.sendMailTo.value = ''
      })
    }
  }

  function init () {
    if (!cacheDOM()) {
      return
    }
    eventListeners()
  }

  init()
}

/**
 * Handles toolbar toggling in email preview template.
 */
function toggleResultControls () {
  const DOM = {}
  const selectors = {
    toggler: '.tool-bar-toggler',
    options: '#tool-options',
  }

  function cacheDOM () {
    DOM.toolBarToggler = document.querySelector(selectors.toggler)
    DOM.toolOptions = document.querySelector(selectors.options)
    return Boolean(DOM.toolBarToggler && DOM.toolOptions)
  }

  function toggleToolbar (e) {
    e.preventDefault()
    DOM.toolBarToggler.classList.toggle('hidden')
    DOM.toolOptions.classList.toggle('hidden')
  }

  function eventListeners () {
    DOM.toolBarToggler.addEventListener('click', toggleToolbar)
  }

  function init () {
    if (!cacheDOM()) {
      return
    }
    eventListeners()
  }

  init()
}

document.addEventListener('DOMContentLoaded', () => {
  previewEmails()
  toggleResultControls()
})