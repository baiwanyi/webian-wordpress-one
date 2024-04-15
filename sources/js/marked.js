import { marked } from 'marked';

// const display = new marked(
//     markedHighlight({
//         langPrefix: 'hljs language-',
//         highlight(code, lang, info) {
//             const language = hljs.getLanguage(lang) ? lang : 'plaintext';
//             return hljs.highlight(code, { language }).value;
//         }
//     })
// );

const layout = document.getElementById('layout').innerHTML

document.getElementById('wwpo-layout').innerHTML = marked.parse(layout)


//   const headingTree = toc.create(html);
//   console.log(headingTree);

