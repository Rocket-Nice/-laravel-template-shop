import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.Components = {}

window.Components.listbox = function listbox(options) {
  let modelName = options.modelName || 'selected'
  let pointer = useTrackedPointer()

  return {
    init() {
      this.optionCount = this.$refs.listbox.children.length
      this.$watch('activeIndex', (value) => {
        if (!this.open) return

        if (this.activeIndex === null) {
          this.activeDescendant = ''
          return
        }

        this.activeDescendant = this.$refs.listbox.children[this.activeIndex].id
      })
    },
    activeDescendant: null,
    optionCount: null,
    open: false,
    activeIndex: null,
    selectedIndex: 0,
    get active() {
      return this.items[this.activeIndex]
    },
    get [modelName]() {
      return this.items[this.selectedIndex]
    },
    choose(option) {
      this.selectedIndex = option
      this.open = false
    },
    onButtonClick() {
      if (this.open) return
      this.activeIndex = this.selectedIndex
      this.open = true
      this.$nextTick(() => {
        this.$refs.listbox.focus()
        this.$refs.listbox.children[this.activeIndex].scrollIntoView({ block: 'nearest' })
      })
    },
    onOptionSelect() {
      if (this.activeIndex !== null) {
        this.selectedIndex = this.activeIndex
      }
      this.open = false
      this.$refs.button.focus()
    },
    onEscape() {
      this.open = false
      this.$refs.button.focus()
    },
    onArrowUp() {
      this.activeIndex = this.activeIndex - 1 < 0 ? this.optionCount - 1 : this.activeIndex - 1
      this.$refs.listbox.children[this.activeIndex].scrollIntoView({ block: 'nearest' })
    },
    onArrowDown() {
      this.activeIndex = this.activeIndex + 1 > this.optionCount - 1 ? 0 : this.activeIndex + 1
      this.$refs.listbox.children[this.activeIndex].scrollIntoView({ block: 'nearest' })
    },
    onMouseEnter(evt) {
      pointer.update(evt)
    },
    onMouseMove(evt, newIndex) {
      // Only highlight when the cursor has moved
      // Pressing arrow keys can otherwise scroll the container and override the selected item
      if (!pointer.wasMoved(evt)) return
      this.activeIndex = newIndex
    },
    onMouseLeave(evt) {
      // Only unhighlight when the cursor has moved
      // Pressing arrow keys can otherwise scroll the container and override the selected item
      if (!pointer.wasMoved(evt)) return
      this.activeIndex = null
    },
    ...options,
  }
}

window.Components.menu = function menu(options = { open: false }) {
  let pointer = useTrackedPointer()

  return {
    init() {
      this.items = Array.from(this.$el.querySelectorAll('[role="menuitem"]'))
      this.$watch('open', () => {
        if (this.open) {
          this.activeIndex = -1
        }
      })
    },
    activeDescendant: null,
    activeIndex: null,
    items: null,
    open: options.open,
    focusButton() {
      this.$refs.button.focus()
    },
    onButtonClick() {
      this.open = !this.open
      if (this.open) {
        this.$nextTick(() => {
          this.$refs['menu-items'].focus()
        })
      }
    },
    onButtonEnter() {
      this.open = !this.open
      if (this.open) {
        this.activeIndex = 0
        this.activeDescendant = this.items[this.activeIndex].id
        this.$nextTick(() => {
          this.$refs['menu-items'].focus()
        })
      }
    },
    onArrowUp() {
      if (!this.open) {
        this.open = true
        this.activeIndex = this.items.length - 1
        this.activeDescendant = this.items[this.activeIndex].id

        return
      }

      if (this.activeIndex === 0) {
        return
      }

      this.activeIndex = this.activeIndex === -1 ? this.items.length - 1 : this.activeIndex - 1
      this.activeDescendant = this.items[this.activeIndex].id
    },
    onArrowDown() {
      if (!this.open) {
        this.open = true
        this.activeIndex = 0
        this.activeDescendant = this.items[this.activeIndex].id

        return
      }

      if (this.activeIndex === this.items.length - 1) {
        return
      }

      this.activeIndex = this.activeIndex + 1
      this.activeDescendant = this.items[this.activeIndex].id
    },
    onClickAway($event) {
      if (this.open) {
        const focusableSelector = [
          '[contentEditable=true]',
          '[tabindex]',
          'a[href]',
          'area[href]',
          'button:not([disabled])',
          'iframe',
          'input:not([disabled])',
          'select:not([disabled])',
          'textarea:not([disabled])',
        ]
          .map((selector) => `${selector}:not([tabindex='-1'])`)
          .join(',')

        this.open = false

        if (!$event.target.closest(focusableSelector)) {
          this.focusButton()
        }
      }
    },

    onMouseEnter(evt) {
      pointer.update(evt)
    },
    onMouseMove(evt, newIndex) {
      // Only highlight when the cursor has moved
      // Pressing arrow keys can otherwise scroll the container and override the selected item
      if (!pointer.wasMoved(evt)) return
      this.activeIndex = newIndex
    },
    onMouseLeave(evt) {
      // Only unhighlight when the cursor has moved
      // Pressing arrow keys can otherwise scroll the container and override the selected item
      if (!pointer.wasMoved(evt)) return
      this.activeIndex = -1
    },
  }
}

window.Components.popoverGroup = function popoverGroup() {
  return {
    __type: 'popoverGroup',
    init() {
      let handler = (e) => {
        if (!document.body.contains(this.$el)) {
          window.removeEventListener('focus', handler, true)
          return
        }
        if (e.target instanceof Element && !this.$el.contains(e.target)) {
          window.dispatchEvent(
            new CustomEvent('close-popover-group', {
              detail: this.$el,
            })
          )
        }
      }
      window.addEventListener('focus', handler, true)
    },
  }
}

window.Components.popover = function popover({ open = false, focus = false } = {}) {
  const focusableSelector = [
    '[contentEditable=true]',
    '[tabindex]',
    'a[href]',
    'area[href]',
    'button:not([disabled])',
    'iframe',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
  ]
    .map((selector) => `${selector}:not([tabindex='-1'])`)
    .join(',')

  function focusFirst(container) {
    const focusableElements = Array.from(container.querySelectorAll(focusableSelector))

    function tryFocus(element) {
      if (element === undefined) return

      element.focus({ preventScroll: true })

      if (document.activeElement !== element) {
        tryFocus(focusableElements[focusableElements.indexOf(element) + 1])
      }
    }

    tryFocus(focusableElements[0])
  }

  return {
    __type: 'popover',
    open,
    init() {
      if (focus) {
        this.$watch('open', (open) => {
          if (open) {
            this.$nextTick(() => {
              focusFirst(this.$refs.panel)
            })
          }
        })
      }

      let handler = (e) => {
        if (!document.body.contains(this.$el)) {
          window.removeEventListener('focus', handler, true)
          return
        }
        let ref = focus ? this.$refs.panel : this.$el
        if (this.open && e.target instanceof Element && !ref.contains(e.target)) {
          let node = this.$el
          while (node.parentNode) {
            node = node.parentNode
            if (node.__x instanceof this.constructor) {
              if (node.__x.$data.__type === 'popoverGroup') return
              if (node.__x.$data.__type === 'popover') break
            }
          }
          this.open = false
        }
      }

      window.addEventListener('focus', handler, true)
    },
    onEscape() {
      this.open = false
      if (this.restoreEl) {
        this.restoreEl.focus()
      }
    },
    onClosePopoverGroup(e) {
      if (e.detail.contains(this.$el)) {
        this.open = false
      }
    },
    toggle(e) {
      this.open = !this.open
      if (this.open) {
        this.restoreEl = e.currentTarget
      } else if (this.restoreEl) {
        this.restoreEl.focus()
      }
    },
  }
}

window.Components.radioGroup = function radioGroup({ initialCheckedIndex = 0 } = {}) {
  return {
    value: undefined,
    active: undefined,
    init() {
      let options = Array.from(this.$el.querySelectorAll('input'))

      this.value = options[initialCheckedIndex]?.value

      for (let option of options) {
        option.addEventListener('change', () => {
          this.active = option.value
        })
        option.addEventListener('focus', () => {
          this.active = option.value
        })
      }

      window.addEventListener(
        'focus',
        () => {
          if (!options.includes(document.activeElement)) {
            this.active = undefined
          }
        },
        true
      )
    },
  }
}

window.Components.tabs = function tabs() {
  return {
    selectedIndex: 0,
    init() {
      // Ждем следующий тик чтобы все табы были инициализированы
      this.$nextTick(() => {
        this.checkUrlHash()
      })

      // Слушаем изменения URL (например, при использовании кнопок браузера)
      window.addEventListener('hashchange', () => {
        this.checkUrlHash()
      })
    },

    checkUrlHash() {
      const hash = window.location.hash.substring(1) // убираем #
      if (hash) {
        let tabs = Array.from(this.$el.querySelectorAll('[x-data^="Components.tab("]'))

        // Ищем таб с соответствующим id или data-tab атрибутом
        let targetTab = tabs.find(tab =>
          tab.id === hash ||
          tab.getAttribute('data-tab') === hash ||
          tab.getAttribute('data-anchor') === hash
        )

        if (targetTab) {
          let idx = tabs.indexOf(targetTab)
          this.selectTab(idx, targetTab, false) // false = не обновлять URL
        }
      }
    },

    selectTab(idx, tab, updateUrl = true) {
      let tabs = Array.from(this.$el.querySelectorAll('[x-data^="Components.tab("]'))
      let panels = Array.from(this.$el.querySelectorAll('[x-data^="Components.tabPanel("]'))

      this.selectedIndex = idx

      // Обновляем URL если нужно
      if (updateUrl) {
        const anchor = tab.id || tab.getAttribute('data-tab') || tab.getAttribute('data-anchor')
        if (anchor) {
          // Используем pushState чтобы не вызвать скролл к элементу
          history.pushState(null, null, `#${anchor}`)
        }
      }

      window.dispatchEvent(
        new CustomEvent('tab-select', {
          detail: {
            tab: tab,
            panel: panels[idx],
          },
        })
      )
    },

    onTabClick(event) {
      if (!this.$el.contains(event.detail)) return

      let tabs = Array.from(this.$el.querySelectorAll('[x-data^="Components.tab("]'))
      let idx = tabs.indexOf(event.detail)

      this.selectTab(idx, event.detail, true) // true = обновить URL
    },

    onTabKeydown(event) {
      if (!this.$el.contains(event.detail.tab)) return

      let tabs = Array.from(this.$el.querySelectorAll('[x-data^="Components.tab("]'))
      let tabIndex = tabs.indexOf(event.detail.tab)

      if (event.detail.key === 'ArrowLeft') {
        let newIdx = (tabIndex - 1 + tabs.length) % tabs.length
        this.selectTab(newIdx, tabs[newIdx], true)
      } else if (event.detail.key === 'ArrowRight') {
        let newIdx = (tabIndex + 1) % tabs.length
        this.selectTab(newIdx, tabs[newIdx], true)
      } else if (event.detail.key === 'Home' || event.detail.key === 'PageUp') {
        this.selectTab(0, tabs[0], true)
      } else if (event.detail.key === 'End' || event.detail.key === 'PageDown') {
        this.selectTab(tabs.length - 1, tabs[tabs.length - 1], true)
      }
    },
  }
}

window.Components.tab = function tab(defaultIndex = 0) {
  return {
    selected: false,
    init() {
      let tabs = Array.from(
        this.$el
          .closest('[x-data^="Components.tabs("]')
          .querySelectorAll('[x-data^="Components.tab("]')
      )

      // Сначала устанавливаем по умолчанию
      this.selected = tabs.indexOf(this.$el) === defaultIndex

      // Затем проверяем URL хеш
      this.$nextTick(() => {
        const hash = window.location.hash.substring(1)
        if (hash) {
          const isThisTab = this.$el.id === hash ||
            this.$el.getAttribute('data-tab') === hash ||
            this.$el.getAttribute('data-anchor') === hash
          if (isThisTab) {
            this.selected = true
            // Обновляем selectedIndex в родительском компоненте
            let tabsContainer = this.$el.closest('[x-data^="Components.tabs("]')
            if (tabsContainer) {
              Alpine.$data(tabsContainer).selectedIndex = tabs.indexOf(this.$el)
            }
          }
        }
      })

      this.$watch('selected', (selected) => {
        if (selected) {
          this.$el.focus()
        }
      })
    },
    onClick() {
      window.dispatchEvent(
        new CustomEvent('tab-click', {
          detail: this.$el,
        })
      )
    },
    onKeydown(event) {
      if (['ArrowLeft', 'ArrowRight', 'Home', 'PageUp', 'End', 'PageDown'].includes(event.key)) {
        event.preventDefault()
      }

      window.dispatchEvent(
        new CustomEvent('tab-keydown', {
          detail: {
            tab: this.$el,
            key: event.key,
          },
        })
      )
    },
    onTabSelect(event) {
      this.selected = event.detail.tab === this.$el
    },
  }
}

window.Components.tabPanel = function tabPanel(defaultIndex = 0) {
  return {
    selected: false,
    init() {
      let panels = Array.from(
        this.$el
          .closest('[x-data^="Components.tabs("]')
          .querySelectorAll('[x-data^="Components.tabPanel("]')
      )

      // Сначала устанавливаем по умолчанию
      this.selected = panels.indexOf(this.$el) === defaultIndex

      // Затем проверяем URL хеш
      this.$nextTick(() => {
        const hash = window.location.hash.substring(1)
        if (hash) {
          let tabs = Array.from(
            this.$el
              .closest('[x-data^="Components.tabs("]')
              .querySelectorAll('[x-data^="Components.tab("]')
          )

          let targetTab = tabs.find(tab =>
            tab.id === hash ||
            tab.getAttribute('data-tab') === hash ||
            tab.getAttribute('data-anchor') === hash
          )

          if (targetTab) {
            let tabIndex = tabs.indexOf(targetTab)
            this.selected = panels.indexOf(this.$el) === tabIndex
          }
        }
      })
    },
    onTabSelect(event) {
      this.selected = event.detail.panel === this.$el
    },
  }
}

Alpine.data('menu', () => ({
  open: false,
  activeIndex: -1,
  dropdownPosition: 'origin-top-right right-0',
  init() {
    // this.$refs.button.focus();
    this.setDropdownPosition();
  },
  focusButton() {
    // this.$refs.button.focus();
  },
  onButtonClick() {
    this.open = !this.open;
    if (this.open) {
      this.setDropdownPosition();
    }
  },
  onButtonEnter() {
    this.open = true;
    this.$nextTick(() => {
      this.$refs['menuItems'].focus();
      this.setDropdownPosition();
    });
  },
  onArrowUp() {
    if (this.activeIndex > 0) {
      this.activeIndex--;
    } else {
      this.activeIndex = this.$refs['menuItems'].children.length - 1;
    }
  },
  onArrowDown() {
    if (this.activeIndex < this.$refs['menuItems'].children.length - 1) {
      this.activeIndex++;
    } else {
      this.activeIndex = 0;
    }
  },
  onClickAway(event) {
    if (!this.$refs['menuItems'].contains(event.target) && !event.target.closest('input, textarea, select, button')) {
      this.open = false;
      this.focusButton();
    }
  },
  onMouseEnter(event) {
    this.activeIndex = Array.from(this.$refs['menuItems'].children).indexOf(event.target.closest('a, button'));
  },
  onMouseMove(event, index) {
    this.activeIndex = index;
  },
  onMouseLeave(event) {
    this.activeIndex = -1;
  },
  setDropdownPosition() {
    this.$refs['menuItems'].style.display = 'block';
    const buttonRect = this.$refs.button.getBoundingClientRect();
    const menuRect = this.$refs['menuItems'].getBoundingClientRect();
    this.$refs['menuItems'].style.display = 'none';

    // Check if there is enough space below the button
    if (window.innerHeight - buttonRect.bottom < menuRect.height) {
      this.dropdownPosition = 'origin-bottom-right bottom-full mb-2';
    } else {
      this.dropdownPosition = 'origin-top-right top-full mt-2';
    }

    // Check if there is enough space on the right of the button
    if (buttonRect.right < menuRect.width) {
      this.dropdownPosition += ' left-0';
    } else {
      this.dropdownPosition += ' right-0';
    }
  }
}));


Alpine.data('surveyLoader', () => ({
  show: false,
  surveyElem: null,
  init() {
    this.surveyElem = this.$refs.survey
  },
  loadSurvey(route) {
    fetchData(route, 'POST', {}).then(response => {
      response.forEach(answer => {
        let container = document.createElement('div')
        let elem = document.createElement('div')
        let question = document.createElement('div')
        question.textContent = answer.question.text
        let score = document.createElement('div')
        if(answer.score >= 9) {
          score.className = 'badge-green whitespace-nowrap'
        } else if(answer.score >= 7) {
          score.className = 'badge-yellow whitespace-nowrap'
        } else {
          score.className = 'badge-red whitespace-nowrap'
        }
        score.textContent = answer.score;
        let commentElem;
        if(answer.comment){
          commentElem = document.createElement('div')
          commentElem.textContent = answer.comment;
          commentElem.style.marginTop = '10px'
        }
        container.className = 'border-b border-gray-200 py-1 my-1'
        elem.className = 'flex justify-between'
        elem.appendChild(question)
        elem.appendChild(score)
        container.appendChild(elem)
        if(commentElem){
          container.appendChild(commentElem)
        }

        this.surveyElem.appendChild(container)
      })
      console.log('response', response)
    }).catch(error => {
      console.log(error);
    });
  }
}));

Alpine.data('surveyStatus', () => ({
  color: null,
  statuses: {
    0: 'bg-gray-200',
    1: 'bg-yellow-200',
    2: 'bg-green-200',
  },
  init(){
    this.color = this.statuses[this.$el.value];
  },
  setStatus(route) {
    const status = this.$el.value;
    fetchData(route, 'POST', {status}).then(response => {
      const select = this.$el;
      select.selectedIndex = Array.from(select.options).findIndex(opt => opt.value == status);
      this.color = this.statuses[this.$el.value];
    }).catch(error => {
      console.log(error);
    });
  }
}));
Alpine.data('wishesStatus', () => ({
  color: null,
  statuses: {
    0: 'bg-gray-200',
    1: 'bg-yellow-200',
    2: 'bg-green-200',
  },
  init(){
    this.color = this.statuses[this.$el.value];
  },
  setStatus(route) {
    const status = this.$el.value;
    fetchData(route, 'POST', {status}).then(response => {
      const select = this.$el;
      select.selectedIndex = Array.from(select.options).findIndex(opt => opt.value == status);
      this.color = this.statuses[this.$el.value];
    }).catch(error => {
      console.log(error);
    });
  }
}));


Alpine.data('exceptionsForm', (initial = [], datepickerSelector = '.datepicker') => ({
  exceptions: Array.isArray(initial) && initial.length
    ? initial.map(i => ({ date_from: i.date_from || '', date_until: i.date_until || '' }))
    : [],
  init() {
    this.$nextTick(() => this.initDatepickers());
  },
  add() {
    this.exceptions.push({ date_from: '', date_until: '' });
    this.$nextTick(() => this.initDatepickers());
  },
  remove(index) {
    this.exceptions.splice(index, 1);
    this.$nextTick(() => this.initDatepickers());
  },
  clearAll() {
    this.exceptions = [];
    this.$nextTick(() => this.initDatepickers());
  },
  initDatepickers() {
    setTimeout(()=>{
      var datepickerFields = document.querySelectorAll('input.datepicker');
      datepickerFields.forEach((field)=>{
        if(!field.classList.contains('datepicker-started')){
          datepickerInit(field)
        }
      })
    },1000)
  }
}));

Alpine.start();

function autoResize(textarea) {
  textarea.style.height = 'auto';
  textarea.style.height = textarea.scrollHeight + 2 + 'px';
}

// Get the textarea element
const textareaFields = document.querySelectorAll('textarea.auto-height');

textareaFields.forEach((textarea) => {
  autoResize(textarea);
  textarea.addEventListener('input', function() {
    autoResize(this);
  });
})

import './helper';
import './admin/common';
import './admin/lfm';
import {fetchData} from "./public/utilites";

