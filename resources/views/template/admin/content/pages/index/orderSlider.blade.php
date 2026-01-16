
@if(isset($content->carousel_data['weRecommend'])&&!empty($content->carousel_data['weRecommend']))
  <div class="mb-5">
    <div class="form-group">
      <x-input-label :value="__('Слайдер с товарами')"/>
      <div id="weRecommend" class="draggable-list max-w-md mt-2"
           data-order-field="order-weRecommend"
           data-items='@json(getOrderSliderJson($products, $content->carousel_data['weRecommend']))'></div>
    </div>
  </div>
@endif
@if(isset($content->carousel_data['weRecommend2'])&&!empty($content->carousel_data['weRecommend2']))
  <div class="mb-5">
    <div class="form-group">
      <x-input-label :value="__('ТОП продукты для отпуска')"/>
      <div id="weRecommend2" class="draggable-list max-w-md mt-2"
           data-order-field="order-weRecommend2"
           data-items='@json(getOrderSliderJson($products, $content->carousel_data['weRecommend2']))'></div>
    </div>
  </div>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const listContainers = document.querySelectorAll('.draggable-list');
    let draggedItem = null;
    let currentContainer = null;

    function createListItems(container) {
      const orderField = container.dataset.orderField;
      const items = JSON.parse(container.dataset.items);

      container.innerHTML = '';
      items.forEach((item, index) => {
        const itemElement = document.createElement('div');
        itemElement.className = 'flex items-center mb-1 bg-gray-300 rounded shadow cursor-move';
        itemElement.innerHTML = `
        <div class="flex items-center" draggable="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grip-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#aaa" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
            <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
          </svg>
          <div class="w-full p-2">
            <span class="text-gray-800">${item.text}</span>
            <input type="hidden" name="${orderField}[${item.id}]" value="${index + 1}">
          </div>
        </div>
      `;

        itemElement.addEventListener('dragstart', dragStart);
        itemElement.addEventListener('dragover', dragOver);
        itemElement.addEventListener('drop', drop);
        itemElement.addEventListener('dragend', dragEnd);

        container.appendChild(itemElement);
      });
    }

    function dragStart(e) {
      draggedItem = this;
      currentContainer = this.closest('.draggable-list');
      setTimeout(() => this.style.opacity = '0.5', 0);
    }

    function dragOver(e) {
      e.preventDefault();
      if (this.closest('.draggable-list') !== currentContainer) return;
      const afterElement = getDragAfterElement(currentContainer, e.clientY);
      if (afterElement == null) {
        currentContainer.appendChild(draggedItem);
      } else {
        currentContainer.insertBefore(draggedItem, afterElement);
      }
    }

    function drop(e) {
      e.preventDefault();
    }

    function dragEnd() {
      this.style.opacity = '1';
      draggedItem = null;
      updateOrder(currentContainer);
      currentContainer = null;
    }

    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll('div:not(.dragging)')];

      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        } else {
          return closest;
        }
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateOrder(container) {
      const items = container.querySelectorAll('div');
      items.forEach((item, index) => {
        const input = item.querySelector('input[type="hidden"]');
        input.value = index + 1;
      });
    }

    // Инициализация всех списков
    listContainers.forEach(container => {
      createListItems(container);
    });
  });
</script>
