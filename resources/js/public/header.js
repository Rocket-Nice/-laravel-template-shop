document.addEventListener('DOMContentLoaded', function () {
// dropdown main menu
  let dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  let dropdownMenus = document.querySelectorAll(".dropdown-menu");

  dropdownToggles.forEach((toggle, index) => {
    toggle.addEventListener("click", function (event) {
      event.preventDefault();
      toggleMenu(index);
    });

    // Учитываем события клика на дочерних элементах toggle
    toggle.querySelectorAll("*").forEach(child => {
      child.addEventListener("click", function (childEvent) {
        childEvent.stopPropagation();
        childEvent.preventDefault();
        toggleMenu(index);
      });
    });
  });

  function toggleMenu(index) {
    // Закроем все меню, кроме текущего
    dropdownMenus.forEach((menu, menuIndex) => {
      if (menuIndex !== index) {
        menu.style.display = "none";
      }
    });

    // Откроем или закроем текущее меню
    let menu = dropdownMenus[index];
    if (menu.style.display === "block") {
      menu.style.display = "none";
    } else {
      menu.style.display = "block";
    }
  }

// Закрыть меню при клике вне его
  document.addEventListener("click", function (event) {
    if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
      dropdownMenus.forEach(menu => {
        menu.style.display = "none";
      });
    }
  });
// dropdown main menu end
// mobile menu
  const menuToggle = document.getElementById('menu-toggle');
  const menuClose = document.getElementById('menu-close');
  const mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle){
    menuToggle.addEventListener('click', () => {
      mobileMenu.style.transform = 'translateY(0)';
    });
  }

  if(menuClose){
    menuClose.addEventListener('click', () => {
      mobileMenu.style.transform = 'translateY(-100%)';
    });
  }

// mobile menu end
// search
  const labelButton = document.querySelectorAll('.label-button')
  if (labelButton.length) {
    labelButton.forEach((button) => {
      if (button.dataset.field) {
        button.addEventListener('click', (e) => {
          let elem = e.target
          let form = elem.closest('form')
          if (!elem.matches('button')) {
            elem = elem.closest('button')
          }
          const field = document.getElementById(elem.dataset.field)
          if (field.value == '') {
            field.focus()
          }else{
            form.submit()
          }
        })
      }
    })

  }
// search end
  const mobileMenuContainer = document.getElementById('mobile-menu-nav');

  const mainMenuItems = document.querySelectorAll('#main-menu > *'); // выберем все прямые дочерние элементы

  mainMenuItems.forEach(item => {
    // Если это просто ссылка без выпадающего меню
    if (item.matches('a')) {
      const listItem = document.createElement('li');
      listItem.innerHTML = `
        <a href="${item.getAttribute('href')}" class="nav-link h-[38px] flex items-center">
          <span>${item.textContent}</span>
        </a>
      `;
      mobileMenuContainer.appendChild(listItem);
      return; // переходим к следующему элементу
    }

    // Если это блок с выпадающим меню
    if (item.classList.contains('dropdown')) {
      const parentLink = item.querySelector('.dropdown-toggle');
      const dropdownLinks = item.querySelectorAll('.dropdown-menu a');

      const listItem = document.createElement('li');
      listItem.innerHTML = `
        <div class="relative group dropdown">
          <button class="nav-parent h-[38px] flex items-center w-full">
            <span>${parentLink.textContent}</span>
            <svg class="nav-arrow ml-[6px] transform" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g filter="url(#filter0_b_1918_1487)">
                <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z" fill="currentColor"/>
              </g>
              <defs>
                <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                  <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                  <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                  <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                  <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487" result="shape"/>
                </filter>
              </defs>
            </svg>
          </button>
          <div class="dropdown-content hidden rounded-lg space-y-3 mt-3 pl-4">
            ${Array.from(dropdownLinks).map(link => {
        const href = link.getAttribute('href');
        const onClick = link.getAttribute('onclick');
        let linkAttributes = `href="${href}" class="nav-link flex items-center"`;
        if (onClick) {
          linkAttributes += ` onclick="${onClick}"`;
        }
        return `<a ${linkAttributes}>${link.textContent}</a>`;
      }).join('')}
          </div>
        </div>
      `;
      mobileMenuContainer.appendChild(listItem);
    }
  });
  // dropdown mobile menu
  document.querySelectorAll('.group').forEach(function(group) {
    const toggleButton = group.querySelector('button');
    if (!toggleButton) {
      return;
    }
    toggleButton.addEventListener('click', function() {
      const arrow = group.querySelector('.nav-parent .nav-arrow');
      if (arrow) {
        arrow.classList.toggle('rotate-180');
      }
      const dropdown = group.querySelector('.dropdown-content');
      if (dropdown) {
        dropdown.classList.toggle('hidden');
      }
    });
  });
  // dropdown mobile menu end
});
