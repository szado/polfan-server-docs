const DocsifyPostmanPayloadPreview = {
    data: {},
    create: function (config) {
        config = config || {};
        this.config = {
            json: config.json || '',
            url: config.url || './webapi-postman-collection.json'
        };
        this.loadData();
        return (hook, vm) => {
            hook.beforeEach(content => this.onMarkdown(content));
        };
    },
    loadData: async function () {
        let items;

        if (this.config.json) {
            items = JSON.parse(this.config.json).item;
        } else {
            // Docsify does not have option for handle async operations such as load json with fetch
            // so this does not make sens because the load success depends on luck
            try {
                const response = await fetch(this.config.url);
                items = (await response.json()).item;
            } catch (e) {
                console.error(`Cannot load Postman Collection data from ${this.config.url}`, e);
                this.data = null;
                return;
            }
        }

        items.forEach(item => this.data[item.name] = item.request.body.raw);
    },
    onMarkdown: function (content) {
        const matches = content.matchAll(/{payload-example (.+?)}/g);
        for (let match of matches) {
            const [shortcode, name] = match;
            let example = this.data[name] || null;
            if (example) {
                // Postman dynamic variables:
                example = example.replaceAll('{{$guid}}', 'e7fa8f5a-aed7-11ec-b909-0242ac120002');
            }
            content = content.replace(shortcode, this.getDetailsHtml(name, example));
        }
        return content;
    },
    getDetailsHtml: function (requestName, examplePayload) {
        return `<details><summary>Przykład <code>${requestName}</code></summary>

\`\`\`json
${examplePayload || 'Niedostępny'}
\`\`\`

</details>`;
    }
};