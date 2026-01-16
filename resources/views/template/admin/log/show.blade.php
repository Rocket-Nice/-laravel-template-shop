<x-light-layout>
  <style>
    body {
      background-color: #1b1b2a;
    }
    .content-wrapper {
      background: transparent;
    }
    #code pre {
      background-color: #1b1b2a;
      padding: 10px;
      font-size: 14px;
      margin: 0;
    }
    #code code {
      color: #cc6633;
      font-family: monospace;
    }
  </style>

  <div id="code">

    <pre>
      <code>
        {{ print_r($log_item->data, true) }}
      </code>
    </pre>

  </div>
</x-light-layout>
