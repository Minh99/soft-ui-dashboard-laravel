

## GUIDE EDIT
### Prompt
File : `app\Http\Services\PromptService.php`
- prompt gen story: **promptGenStory()**
    - type 1 : tự luận
    - type 2 : trắc nghiệm
    => cả 2 type đều dùng chung biến `$countVoc` để lấy ra lượng tự vựng gen ra câu hỏi

    => biến độ dài story `LIMIT_WORDS_GEN = 150`
- prompt gen test 2:
    - câu ngắn fill blank : **promptGenTest2ByType1()**
    - câu dài fill blank  : **promptGenTest2ByType2()** // đã comment lại
    - dịch từ Hàn qua Anh : **promptGenTest2ByType3()**
    - đặt câu theo từ vựng đã cho: **promptGenTest2ByType4()**
    => Trong các hàm trên đều có biến `$take = 8`, giá trị này là số câu hỏi của bài test 2
    
    => Mỗi hàm **Gen** đều có 1 hàm **Submit** ngay bên dưới: vd `promptGenTest2ByType1() => promptSubmitTest2ByType1()`

    **p/s: sửa promt nhưng vẫn đảm bảo giữ format json trả về như cũ để tránh lỗi**


### Flow
 Để fake DB test cho nhanh thì:
 Chỉ cần update trong bảng `user_day_completeds`. không update cột `is_completed = 1`
- Để passed qua ngày **1**:
    - is_passed_first_quiz      = 1
    - is_passed_quiz_story_1    = 1
    - is_passed_quiz_story_2    = 1
    - is_passed_test_2          = 1
    - created_at < current date
- Để passed qua ngày **2, 3, 5**:
    - is_passed_quiz_story_1    = 1
    - is_passed_quiz_story_2    = 1
    - is_passed_test_2          = 1
    - created_at < current date
- Để passed qua ngày **4, 6**:
    - is_passed_first_quiz      = 1
    - is_passed_test_2          = 1
    - created_at < current date

P/s: Edit DB sometime sẽ làm hiển thị data report ở admin lộn xộn, cái này phải chịu thôi =)).

==> không update cột `is_completed`, cột này tự flow code thực hiện khi đủ điều kiện

==> không tự insert record mới, tính ngày sai