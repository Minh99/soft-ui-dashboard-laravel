<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vocabularies = [
            [
                'en' => 'Oscillating between',
                'vi' => 'Do dự',
                'ko' => '둘 중에',
            ],
            [
                'en' => 'Hanging up in the air',
                'vi' => 'Chưa quyết định được',
                'ko' => '미지수에 걸리다',
            ],
            [
                'en' => 'Torn between',
                'vi' => 'Cân nhắc, đang đau đầu giữa việc quyết định',
                'ko' => '갈등하다',
            ],
            [
                'en' => 'Scholarship',
                'vi' => 'Học bổng',
                'ko' => '장학금',
            ],
            [
                'en' => 'a perfect attendance',
                'vi' => 'Đi học đủ',
                'ko' => '완벽한 출석',
            ],
            [
                'en' => 'An all-nighter',
                'vi' => 'Học xuyên đêm',
                'ko' => '밤샘 공부',
            ],
            [
                'en' => 'A cram session',
                'vi' => 'Học gấp rút',
                'ko' => '강박적 공부',
            ],
            [
                'en' => 'Cheat off of',
                'vi' => 'Gian lận',
                'ko' => '표절하다',
            ],
            [
                'en' => 'Taking a crash course',
                'vi' => 'Khoá học ngắn hạn',
                'ko' => '짧은 기간 동안 강의 듣기',
            ],
            [
                'en' => 'Clear your desk',
                'vi' => 'Dọn bàn',
                'ko' => '책상 정리하다',
            ],
            [
                'en' => 'Flunk',
                'vi' => 'Trượt môn',
                'ko' => '누제하다',
            ],
            [
                'en' => 'Roll off',
                'vi' => 'Lăn, rơi',
                'ko' => '떨어지다',
            ],
            [
                'en' => 'Home-schooled',
                'vi ' => 'Học tại nhà',
                'ko' => '가정교육을 받은',
            ],
            [
                'en' => 'Seek and find',
                'vi' => 'Tìm kiếm',
                'ko' => '찾아보다',
            ],
            [
                'en' => 'Make-up class',
                'vi' => 'Lớp học bù',
                'ko' => '보강 수업',
            ],
            [
                'en' => 'Major in',
                'vi' => 'Chuyên ngành chính',
                'ko' => '전공하다',
            ],
            [
                'en' => 'Minor in',
                'vi' => 'Chuyên ngành phụ',
                'ko' => '부전공하다',
            ],
            [
                'en' => 'Re-enter school',
                'vi' => 'Đi học lại',
                'ko' => '재학하다',
            ],
            [
                'en' => 'Come up with',
                'vi' => 'Nảy ra ý tưởng',
                'ko' => '생각해 내다',
            ],
            [
                'en' => 'Plenty of time',
                'vi' => 'Nhiều thời gian',
                'ko' => '여유 많다',
            ],
            [
                'en' => 'A heavy course load',
                'vi' => 'Học cùng một lúc nhiều môn',
                'ko' => '많은 수업 부담',
            ],
            [
                'en' => 'Bunk out',
                'vi' => 'Bùng học',
                'ko' => '수업 땡떙치다',
            ],
            [
                'en' => 'Class clown',
                'vi' => 'Cây hài trong lớp',
                'ko' => '반장난꾼',
            ],
            [
                'en' => 'Term paper',
                'vi' => 'Luận văn',
                'ko' => '학기 논문',
            ],
            [
                'en' => 'Drown in',
                'vi' => 'Chìm vào, tập trung vào',
                'ko' => '에 빠져들다',
            ],
            [
                'en' => 'Pressed for time',
                'vi' => 'Thời gian gấp rút',
                'ko' => '시간 촉박하다',
            ],
            [
                'en' => 'Smear',
                'vi' => 'Vết bẩn, vết ố',
                'ko' => '얼룩',
            ],
            [
                'en' => 'Scan',
                'vi' => 'Scan',
                'ko' => '스캔하다',
            ],
            [
                'en' => 'Due',
                'vi' => 'Sắp đến hạn',
                'ko' => '마감일이 다가오다',
            ],
            [
                'en' => 'Locker room gossip',
                'vi' => 'Tin đồn',
                'ko' => '탈의실소문',
            ],
            [
                'en' => 'Binder clips',
                'vi' => 'Kẹp tài liệu',
                'ko' => '바인더 클립',
            ],
            [
                'en' => 'Loose-leaf book',
                'vi' => 'Sổ không gáy',
                'ko' => '반백(반구)책',
            ],
            [
                'en' => 'Rollerball pens',
                'vi' => 'Bút bi',
                'ko' => '롤러볼 펜',
            ],
            [
                'en' => 'Cushion grip',
                'vi' => 'Đệm cầm tay',
                'ko' => '쿠션 그립',
            ],
            [
                'en' => 'Graducation ceremony',
                'vi' => 'Lễ tốt nghiệp',
                'ko' => '졸업식',
            ],
            [
                'en' => 'Yearbook’s signning',
                'vi' => 'Kí kỷ yếu',
                'ko' => '졸업앨범 서명',
            ],
            [
                'en' => 'Stay focus',
                'vi' => 'Tập trung',
                'ko' => '집중',
            ],
            [
                'en' => 'Teacher’s louge',
                'vi' => 'Phòng nghỉ giáo viên',
                'ko' => '교사실',
            ],
            [
                'en' => 'Rusyproof',
                'vi' => 'Chống nước',
                'ko' => '방수',
            ],
            [
                'en' => 'Align',
                'vi' => 'Sắp xếp',
                'ko' => '정렬하다',
            ],
            [
                'en' => 'Classmate',
                'vi' => 'Bạn học cùng lớp',
                'ko' => '친구반',
            ],
            [
                'en' => 'Bully',
                'vi' => 'Chửi, mắng, nói xấu',
                'ko' => '욕하다',
            ],
            [
                'en' => 'Bunk together',
                'vi' => 'Cùng nhau trải qua một ngày, đi chơi,ăn uống, ngủ cùng',
                'ko' => '같이 지내다, 같이 자다',
            ],
            [
                'en' => 'lick the flap',
                'vi' => 'Liếm dìa phong bì',
                'ko' => '봉투를 빨다',
            ],
            [
                'en' => 'Water-based ink',
                'vi' => 'Mực chống nước',
                'ko' => '수성 잉크',
            ],
            [
                'en' => 'Dozing off',
                'vi' => 'Buồn ngủ',
                'ko' => '졸다',
            ],
        ];

        DB::table('vocabularies')->insert($vocabularies);
    }
}
