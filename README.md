name: Image Resize Automation
on:
  push:
    paths:
      - 'input/**' # input 폴더에 파일이 올라오면 실행

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Set up Python
        uses: actions/setup-python@v4
        with:
          python-version: '3.9'
      - name: Install dependencies
        run: pip install Pillow
      - name: Run resize script
        run: python scripts/resize.py
      - name: Commit and push changes
        run: |
          git config --global user.name 'github-actions'
          git config --global user.email 'github-actions@github.com'
          git add output/
          git commit -m "Auto-resize images" || exit 0
          git push
