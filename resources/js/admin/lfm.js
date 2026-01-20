window.fieldImages = (items) => {
  if (typeof window.target_input != "undefined"){
    const target_input = document.getElementById(window.target_input);
    var file_path = items
      .map(function (item) {
        return item.url;
      })
      .join(",");
    target_input.value = file_path;
    target_input.dispatchEvent(new Event("change"));
  }
  if (typeof window.target_preview != "undefined"){
    const target_preview = document.getElementById(window.target_preview).querySelector('.img');
    const target_input_thumb = document.getElementById(window.target_input_preview);
    target_preview.innerHTML = "";

    items.forEach(function (item) {
      // let full_image = document.createElement("a");
      // full_image.href = 'javascript:;';
      // full_image.style.display = 'inline-block';
      // full_image.setAttribute('data-fancybox', true);
      // full_image.setAttribute('data-src', item.url);
      let img = document.createElement("img");
      img.style.height = 'height: 5rem';
      img.src = item.url;
      img.className = 'overflow-hidden max-w-full object-cover object-center'
      // full_image.appendChild(img);
      target_preview.appendChild(img);
      target_input_thumb.value = item.thumb_url;

      Fancybox.bind('[data-fancybox]',{
        Toolbar: {
          display: {
            left: ["infobar"],
            middle: [],
            right: ["close"],
          },
        },
      });
    });

    target_preview.dispatchEvent(new Event("change"));
  }
  Fancybox.close();
}
window.choosedImages = (items) => {
  // if (typeof window.target_input != "undefined"){
  //   const target_input = document.getElementById(window.target_input);
  //   var file_path = items
  //     .map(function (item) {
  //       return item.url;
  //     })
  //     .join(",");
  //   var thumbs_path = items
  //     .map(function (item) {
  //       return item.thumb_url;
  //     })
  //     .join(",");
  //   target_input.value = file_path;
  //   target_input_thumb.value = thumbs_path;
  //   target_input.dispatchEvent(new Event("change"));
  // }
  if (typeof window.target_preview != "undefined"){
    const target_preview = document.getElementById(window.target_preview)
    const input_name = target_preview.dataset.name

    //const target_input_thumb = document.getElementById(window.target_input_preview);
    target_preview.innerHTML = "";

    items.forEach(function (item, index) {
      const target_input_thumb = document.createElement('input')
      target_input_thumb.type = 'hidden'
      const target_input = document.createElement('input')
      target_input.type = 'hidden'
      if(items.length > 1){
        target_input_thumb.name = `${input_name}[${index}][thumb]`
        target_input.name = `${input_name}[${index}][img]`
      }else{
        target_input_thumb.name = `${input_name}[thumb]`
        target_input.name = `${input_name}[img]`
      }
      target_input.value = item.url
      target_input_thumb.value = item.thumb_url

      let full_image = document.createElement("a");
      full_image.href = 'javascript:;';
      full_image.className = 'block rounded-md overflow-hidden w-20 m-2';
      full_image.setAttribute('data-fancybox', true);
      full_image.setAttribute('data-src', item.url);
      let img = document.createElement("img");
      img.style.height = 'height: 5rem';
      img.src = item.thumb_url;
      img.className = 'overflow-hidden max-w-full object-cover object-center'
      full_image.appendChild(img);
      target_preview.appendChild(full_image);
      target_preview.appendChild(target_input);
      target_preview.appendChild(target_input_thumb);

      Fancybox.bind('[data-fancybox]',{
        Toolbar: {
          display: {
            left: ["infobar"],
            middle: [],
            right: ["close"],
          },
        },
      });
    });

    // target_preview.dispatchEvent(new Event("change"));
  }
  Fancybox.close();
}
window.choosedFiles = (items) => {
  if (typeof window.target_preview != "undefined"){
    const target_preview = document.getElementById(window.target_preview)
    const input_name = target_preview.dataset.name
    // console.log(input_name);
    //const target_input_thumb = document.getElementById(window.target_input_preview);
    target_preview.innerHTML = "";
    items.forEach(function (item, index) {
      const target_input = document.createElement('input')
      target_input.type = 'hidden'
      if(items.length > 1){
        target_input.name = `${input_name}[files][${index}][file]`
      }else{
        target_input.name = `${input_name}[file]`
      }
      target_input.value = item.url
      target_preview.appendChild(target_input);
    });
    var html = '<ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">';
    items.forEach((item) => {
      html += '<li>' + item.name + '</li>';
    })
    html += '</ul>'
    const fileList = document.createElement('div')
    fileList.innerHTML = html
    target_preview.appendChild(fileList);
   // target_preview.dispatchEvent(new Event("change"));
  }
  Fancybox.close();
}
window.choosedVideo = (items) => {
  if (typeof window.target_preview != "undefined"){
    const target_preview = document.getElementById(window.target_preview)
    const input_name = target_preview.dataset.name
    //const target_input_thumb = document.getElementById(window.target_input_preview);
    target_preview.innerHTML = "";
    items.forEach(function (item, index) {
      const target_input = document.createElement('input')
      target_input.type = 'hidden'
      if(items.length > 1){
        target_input.name = `${input_name}[${index}][file]`
      }else{
        target_input.name = `${input_name}[file]`
      }
      target_input.value = item.url
      target_preview.appendChild(target_input);
    });
    var html = '<ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">';
    items.forEach((item) => {
      html += '<li>' + item.name + '</li>';
    })
    html += '</ul>'
    const fileList = document.createElement('div')
    fileList.innerHTML = html
    target_preview.appendChild(fileList);
   // target_preview.dispatchEvent(new Event("change"));
  }
  Fancybox.close();
}
