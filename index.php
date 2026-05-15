<?php
/**
 * 1000x1000 JPG 규격화 프로그램
 */

// 1. 설정
$input_folder  = 'input/';   // 원본 이미지를 넣는 폴더
$output_folder = 'output/';  // 변환된 이미지가 저장될 폴더
$target_w      = 1000;       // 가로 규격
$target_h      = 1000;       // 세로 규격
$jpg_quality   = 90;         // 출력 화질 (0~100)

// 폴더가 없으면 생성
if (!is_dir($output_folder)) mkdir($output_folder, 0777, true);
if (!is_dir($input_folder))  mkdir($input_folder, 0777, true);

// 2. 파일 스캔
$files = scandir($input_folder);
$process_count = 0;

echo "<h2>이미지 규격화 작업 시작</h2>";

foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;

    $file_path = $input_folder . $file;
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // 처리 가능 이미지 확장자 확인
    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        
        // 이미지 타입에 따른 리소스 생성
        switch ($extension) {
            case 'jpg': case 'jpeg': $src = imagecreatefromjpeg($file_path); break;
            case 'png':  $src = imagecreatefrompng($file_path); break;
            case 'gif':  $src = imagecreatefromgif($file_path); break;
            case 'webp': $src = imagecreatefromwebp($file_path); break;
            default: continue 2;
        }

        if (!$src) continue;

        // 3. 1000x1000 흰색 배경 캔버스 생성
        $dst = imagecreatetruecolor($target_w, $target_h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        // 4. 비율 유지 리사이징 계산 (중앙 배치)
        $old_w = imagesx($src);
        $old_h = imagesy($src);
        $scale = min($target_w / $old_w, $target_h / $old_h);
        
        $new_w = (int)($old_w * $scale);
        $new_h = (int)($old_h * $scale);
        $dst_x = (int)(($target_w - $new_w) / 2);
        $dst_y = (int)(($target_h - $new_h) / 2);

        // 이미지 복사 및 크기 조절
        imagecopyresampled($dst, $src, $dst_x, $dst_y, 0, 0, $new_w, $new_h, $old_w, $old_h);

        // 5. JPG 파일로 저장 (파일명 그대로 유지하되 확장자만 .jpg로 변경)
        $new_filename = pathinfo($file, PATHINFO_FILENAME) . '.jpg';
        imagejpeg($dst, $output_folder . $new_filename, $jpg_quality);

        // 메모리 해제
        imagedestroy($src);
        imagedestroy($dst);
        
        echo "완료: {$file} -> {$new_filename}<br>";
        $process_count++;
    }
}

echo "<hr><p>총 {$process_count}개의 파일이 변환 완료되었습니다.</p>";
?>