# Templavloila to fluid and dce

Tool , based on extbase & fluid. It is editor friendly, default integration of social sharing and many other features.

## Requirements

- TYPO3 7.6 - 8.x (use branch ``6x`` for TYPO3 6.2 LTS)

## Documentation

This extension is not so automatic. you can customize it as it is required.

### Installation

1) Install the extension by using the extension manager , dynamic content element is required


### Usage

1) Install dynamic content element (dce)

2) Copy uploads/tx_templavoila to fileadmin/ and create Update storage index (scheduler) to index all of files from templavoila

3) Admin Tools -> Templavoila to fluid . 1. change the templates to fluid html (/fileadmin/templates/fluid/)

4) 2. FCE to DCE, DCE will be automatically created and mapped to fluid html.

5) 3. Remapping FCE to DCE, choose the FCE from templavoila and responding DCE, then all of contents will be changed to DCE correctly.

6) that can not solve all of the problems, you should add backend_layout and set page to fluid Template with typoscript.

7) fluid html should also be modified manually to render correct content.

