<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="description" content="Description">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/docsify@4/lib/themes/dark.css">
  <script src="//cdn.jsdelivr.net/npm/docsify-edit-on-github"></script>
  <script src="./js/docsify-postman-payload-preview.js"></script>
  <script id="postman-collection" type="application/json">
      <?=file_get_contents('./webapi-postman-collection.json')?>
  </script>
  <style>
    .markdown-section tr:nth-child(2n) {
      background-color: #484848;
    }
    .markdown-section p.tip code {
      background-color: #1a1a1a;
    }
    details > summary {
      list-style-type: '▶️ ';
      cursor: pointer;
    }
    details[open] > summary {
      list-style-type: '🔽 ';
    }
  </style>
</head>
<body>
  <div id="app"></div>
  <script>
    window.$docsify = {
      name: 'PolfanServer v3 API',
      repo: 'https://github.com/szado/polfan-server-docs',
      loadSidebar: true,
      alias: {
        '/.*/_sidebar.md': '/_sidebar.md'
      },
      plugins: [
        DocsifyPostmanPayloadPreview.create({json: document.querySelector('#postman-collection').innerText}),
        EditOnGithubPlugin.create('https://github.com/szado/polfan-server-docs/blob/master/', null, 'Edytuj na GitHubie'),
      ]
    }
  </script>
  <!-- Docsify v4 -->
  <script src="//cdn.jsdelivr.net/npm/docsify@4"></script>
  <script src="//cdn.jsdelivr.net/npm/prismjs@1/components/prism-json.min.js"></script>
</body>
</html>
