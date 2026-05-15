<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이미지 1000x1000 규격화 도구</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        #drop-zone { width: 100%; max-width: 500px; height: 200px; border: 3px dashed #ccc; margin: 20px auto; display: flex; align-items: center; justify-content: center; background: white; border-radius: 10px; cursor: pointer; }
        #drop-zone.hover { border-color: #3498db; background: #ebf5fb; }
        .btn { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        #result-container { margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        canvas { display: none; }
        .preview-item { background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .preview-item img { width: 100%; height: auto; border-radius: 3px; }
        .preview-item p { font-size: 12px; margin: 5px 0; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>

    <h1>이미지 규격화 (1000x1000 JPG)</h1>
    <p>사진을 아래 영역에 드래그하거나 클릭해서 선택하세요.</p>

    <div id="drop-zone">파일을 여기에 끌다 놓으세요</div>
    <input type="file" id="file-input" multiple accept="image/*" style="display: none;">
    <button class="btn" onclick="document.getElementById('file-input').click()">사진 선택하기</button>

    <div id="result-container"></div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const resultContainer = document.getElementById('result-container');

        // 드래그 앤 드롭 이벤트
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('hover'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('hover'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('hover');
            processFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => processFiles(e.target.files));

        function processFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = 1000;
                        canvas.height = 1000;

                        // 배경 흰색 채우기
                        ctx.fillStyle = "white";
                        ctx.fillRect(0, 0, 1000, 1000);

                        // 비율 유지하며 중앙 배치 계산
                        const scale = Math.min(1000 / img.width, 1000 / img.height);
                        const x = (1000 - img.width * scale) / 2;
                        const y = (1000 - img.height * scale) / 2;
                        const width = img.width * scale;
                        const height = img.height * scale;

                        ctx.drawImage(img, x, y, width, height);

                        // JPG로 변환 및 다운로드 링크 생성
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                        displayResult(dataUrl, file.name);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        function displayResult(dataUrl, originalName) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            
            const newName = originalName.substring(0, originalName.lastIndexOf('.')) || originalName;
            
            div.innerHTML = `
                <img src="${dataUrl}">
                <p>${newName}.jpg</p>
                <a href="${dataUrl}" download="${newName}.jpg" style="font-size: 12px; color: #3498db;">다운로드</a>
            `;
            resultContainer.appendChild(div);
        }
    </script>
</body>
</html>
