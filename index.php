<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Devana API</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="description" content="Dokumentacja protokołu API Devany">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/docsify@4/lib/themes/dark.css">
  <script src="//cdn.jsdelivr.net/npm/docsify-edit-on-github"></script>
  <style>
    .markdown-section tr:nth-child(2n) {
      background-color: #484848;
    }
    .markdown-section p.tip code {
      background-color: #1a1a1a;
    }
    .markdown-section table {
      display: table;
      width: 100%;
    }
    .markdown-section td, .markdown-section th {
      vertical-align: top;
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
      name: 'Devana API',
      repo: 'https://github.com/szado/polfan-server-docs',
      loadSidebar: true,
      subMaxLevel: 0,
      alias: {
        '/.*/_sidebar.md': '/_sidebar.md'
      },
      search: {
        placeholder: 'Szukaj...',
        noData: 'Brak wyników',
        depth: 3
      },
      plugins: [
        EditOnGithubPlugin.create('https://github.com/szado/polfan-server-docs/blob/master/', null, 'Edytuj na GitHubie'),
      ]
    }
  </script>
  <!-- Docsify v4 -->
  <script src="//cdn.jsdelivr.net/npm/docsify@4"></script>
  <script src="//cdn.jsdelivr.net/npm/docsify@4/lib/plugins/search.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/docsify-copy-code@2"></script>
  <script src="//cdn.jsdelivr.net/npm/prismjs@1/components/prism-json.min.js"></script>
</body>
</html>
