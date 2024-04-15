
// export default class toc {

// import jQuery = require("jquery");

//     static create = (rootElement) => {
//         const headings = rootElement.getElementsByTagName('h1');
//         // 初始化目录树为一个数组，包含所有<h1>标签作为根节点
//         const tree = Array.from(headings).map(h1 => ({
//             tag: 'h1',
//             text: h1.textContent.trim(),
//             children: []
//         }));

//         // 遍历所有<h2>到<h6>标签
//         for (let i = 2; i <= 6; i++) {
//             const currentHeadings = rootElement.getElementsByTagName(`h${i}`);
//             let currentLevel = tree; // 当前处理的层级，初始为根节点数组

//             Array.from(currentHeadings).forEach(heading => {
//                 const headingText = heading.textContent.trim();
//                 const parentElement = heading.parentElement;

//                 // 找到当前标题应该插入的父级标题
//                 while (parentElement && !parentElement.matches(`h${i - 1}`)) {
//                     parentElement = parentElement.parentElement;
//                 }

//                 if (parentElement) {
//                     // 遍历当前层级的目录树，找到对应的父节点
//                     let found = false;
//                     currentLevel.forEach((node, index) => {
//                         if (node.text === parentElement.textContent.trim()) {
//                             // 将当前标题添加到父节点的children数组中
//                             node.children.push({
//                                 tag: `h${i}`,
//                                 text: headingText,
//                                 children: []
//                             });
//                             found = true;
//                         } else if (node.children.length > 0) {
//                             // 如果父节点有子节点，则递归查找
//                             const childLevel = node.children;
//                             found = toc.search(childLevel, headingText, `h${i}`);
//                         }
//                     });

//                     if (!found) {
//                         // 如果没有找到父节点，可能是因为HTML结构有误或缺失了某些标题
//                         console.warn(`No parent found for heading: ${headingText}`);
//                     }
//                 } else {
//                     // 如果没有找到父级<h>标签，可能是因为HTML结构有误或缺失了某些标题
//                     console.warn(`No parent heading found for: ${headingText}`);
//                 }
//             });

//             // 更新currentLevel为当前层级的最深层子节点数组，用于处理下一层级的标题
//             currentLevel = tree.flatMap(node => node.children);
//         }

//         return tree;
//     }

//     // 辅助函数，用于递归查找并添加子节点
//     static search = (level, text, tag) => {
//         let found = false;
//         level.forEach((node, index) => {
//             if (node.text === text) {
//                 // 如果已经存在相同的标题，则不重复添加，可以根据需要修改为合并或更新节点
//                 found = true;
//             } else if (node.children.length > 0) {
//                 // 如果节点有子节点，则递归查找
//                 found = toc.search(node.children, text, tag);
//             } else {
//                 // 如果没有子节点，则直接添加
//                 node.children.push({ tag, text, children: [] });
//                 found = true;
//             }
//         });
//         return found;
//     }
// }

// function generateHeadingTree() {
//     // 初始化目录树为一个空数组
//     const tree = [];

//     // 遍历所有的h1到h6标签
//     for (let i = 1; i <= 6; i++) {
//       const selector = `#wwpo-layout h${i}`;
//       jQuery(selector).each(function() {
//         // 获取当前标题的文本和标签名
//         const text = jQuery(this).text().trim();
//         const tagName = this.tagName.toLowerCase();

//         // 获取当前标题的层级（基于标签名h1, h2, ..., h6）
//         const level = parseInt(tagName.substr(1), 10);

//         // 递归函数，用于构建目录树结构
//         function buildTree(parent, heading) {
//           // 如果当前标题的层级大于父节点的层级，则添加为子节点
//           if (heading.level > parent.level) {
//             if (!parent.children) {
//               parent.children = [];
//             }
//             const childNode = { text, level: heading.level, children: [] };
//             parent.children.push(childNode);
//             buildTree(childNode, heading); // 递归处理当前标题的子标题
//           } else if (heading.level < parent.level) {
//             // 如果当前标题的层级小于父节点的层级，则需要向上回溯
//             buildTree(findParent(parent, heading.level - 1), heading);
//           }
//         }

//         // 辅助函数，用于在目录树中找到指定层级的父节点
//         function findParent(node, targetLevel) {
//           if (node.level === targetLevel) {
//             return node;
//           } else if (node.children) {
//             for (let child of node.children) {
//               const result = findParent(child, targetLevel);
//               if (result) {
//                 return result;
//               }
//             }
//           }
//           return null;
//         }

//         // 初始化当前标题对象
//         const heading = { text, level };

//         // 寻找或创建根节点（对于h1标签）
//         if (i === 1) {
//           tree.push({ text, level, children: [] });
//         } else {
//           // 对于h2到h6标签，构建或更新目录树
//           const root = tree.find(node => node.level === 1); // 假设h1是根节点
//           if (root) {
//             buildTree(root, heading);
//           } else {
//             console.warn('No root node (h1) found to build the heading tree.');
//           }
//         }
//       });
//     }

//     return tree;
//   }



  function generateHeadingTree() {
    // 初始化目录树为一个空数组
    let tree = [];
    let currentLevel = null;

    // 遍历所有的h1到h6标签
    for (let i = 1; i <= 6; i++) {
        $(`#wwpo-layout h${i}`).each(function() {
            const text = $(this).text().trim();
            const level = i;

            // 创建新的树节点
            const newNode = { text, level, children: [] };

            // 如果当前层级与之前的层级相同，则将新节点添加到当前层级的父节点的子节点中
            if (level === currentLevel) {
                tree[tree.length - 1].children.push(newNode);
            } else if (level < currentLevel) {
                // 如果当前层级小于之前的层级，则需要向上回溯
                let parent = tree;
                for (let j = currentLevel; j > level; j--) {
                    parent = parent[parent.length - 1].children;
                }
                parent.push(newNode);
            } else {
                // 如果当前层级大于之前的层级，则将新节点作为新层级添加到树中
                tree.push(newNode);
            }

            // 更新当前层级
            currentLevel = level;
        });
    }

    // setTimeout(() => {
    //     JSON.stringify(tree, null, 2)
    // }, 500);

    // 打印目录树
    console.log(tree);
}

// 使用函数生成目录树
  setTimeout(() => {
    const headingTree = generateHeadingTree();
    console.log(headingTree);
  }, 500);
