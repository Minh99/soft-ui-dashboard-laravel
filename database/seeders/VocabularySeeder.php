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
        // INSERT INTO vocabularies (id, en, ko, vi, created_at, updated_at)
        // VALUES (1,"Dozing off", "깜빡 졸다", null, now(), now()),
        // (2,"Best Friend", "친한 친구", null, now(), now()),
        // (3,"Pass with flying colors", "좋은 성과를 이루었다", null, now(), now()),
        // (4,"Bully", "욕하다, 괴롭히다", null, now(), now()),
        // (5,"Classmate", "반친구", null, now(), now()),
        // (6,"Procrastination", "미루기,지연", null, now(), now()),
        // (7,"Achievement", "성과,성취", null, now(), now()),
        // (8,"Stay focus", "에 집중하다", null, now(), now()),
        // (9,"Yearbook album", "졸업 앨범", null, now(), now()),
        // (10,"Graduation ceremony", "졸업식", null, now(), now()),
        // (11,"Frustration", "좌절", null, now(), now()),
        // (12,"Support", "지원, 지지하다", null, now(), now()),
        // (13,"Loose-leaf book", "낱장 묶음책", null, now(), now()),
        // (14,"Locker room gossip", "틸의실 험담", null, now(), now()),
        // (15,"Timetable", "시간표", null, now(), now()),
        // (16,"Enroll", "등록하다", null, now(), now()),
        // (17,"Confidence", "자신감", null, now(), now()),
        // (18,"Pressed for time", "시간 쫓기다", null, now(), now()),
        // (19,"Drown in", "예 빠져들다", null, now(), now()),
        // (20,"Term paper", "학기 논문", null, now(), now()),
        // (21,"Class Clown", "반장난꾼", null, now(), now()),
        // (22,"Bunk out", "수업 땡땡치다", null, now(), now()),
        // (23,"Dormitory", "기숙사", null, now(), now()),
        // (24,"Grade", "성적", null, now(), now()),
        // (25,"Come up with", "생각해 내다", null, now(), now()),
        // (26,"Re-enter school", "재입학하다", null, now(), now()),
        // (27,"Minor in", "부전공하다", null, now(), now()),
        // (28,"Major in", "전공하다", null, now(), now()),
        // (29,"Make-up class", "보강 수업", null, now(), now()),
        // (30,"Zone out", "맹해지다, 집중에 잃다", null, now(), now()),
        // (31,"Bookworm", "모범생", null, now(), now()),
        // (32,"Hang out", "놀러가다", null, now(), now()),
        // (33,"Clear sb desk", "책상 정리하다", null, now(), now()),
        // (34,"Lack of foundation", "기초 부족", null, now(), now()),
        // (35,"Cheat off of", "시험에 부정행위하다", null, now(), now()),
        // (36,"Pull an all-nighter", "밤샘공부", null, now(), now()),
        // (37,"Perfect attendance", "완벽한 출석, 개근", null, now(), now()),
        // (38,"Scholarship", "장학금", null, now(), now()),
        // (39,"Torn between", "~ 사이에 갈등하다", null, now(), now()),
        // (40,"Principal", "교장", null, now(), now());

        $vocabularies = [
            [
                'en' => 'Dozing off',
                'vi' => null,
                'ko' => '깜빡 졸다'
            ],
            [
                'en' => 'Best Friend',
                'vi' => null,
                'ko' => '친한 친구'
            ],
            [
                'en' => 'Pass with flying colors',
                'vi' => null,
                'ko' => '좋은 성과를 이루었다'
            ],
            [
                'en' => 'Bully',
                'vi' => null,
                'ko' => '욕하다, 괴롭히다'
            ],
            [
                'en' => 'Classmate',
                'vi' => null,
                'ko' => '반친구'
            ],
            [
                'en' => 'Procrastination',
                'vi' => null,
                'ko' => '미루기,지연'
            ],
            [
                'en' => 'Achievement',
                'vi' => null,
                'ko' => '성과,성취'
            ],
            [
                'en' => 'Stay focus',
                'vi' => null,
                'ko' => '에 집중하다'
            ],
            [
                'en' => 'Yearbook album',
                'vi' => null,
                'ko' => '졸업 앨범'
            ],
            [
                'en' => 'Graduation ceremony',
                'vi' => null,
                'ko' => '졸업식'
            ],
            [
                'en' => 'Frustration',
                'vi' => null,
                'ko' => '좌절'
            ],
            [
                'en' => 'Support',
                'vi' => null,
                'ko' => '지원, 지지하다'
            ],
            [
                'en' => 'Loose-leaf book',
                'vi' => null,
                'ko' => '낱장 묶음책'
            ],
            [
                'en' => 'Locker room gossip',
                'vi' => null,
                'ko' => '틸의실 험담'
            ],
            [
                'en' => 'Timetable',
                'vi' => null,
                'ko' => '시간표'
            ],
            [
                'en' => 'Enroll',
                'vi' => null,
                'ko' => '등록하다'
            ],
            [
                'en' => 'Confidence',
                'vi' => null,
                'ko' => '자신감'
            ],
            [
                'en' => 'Pressed for time',
                'vi' => null,
                'ko' => '시간 쫓기다'
            ],
            [
                'en' => 'Drown in',
                'vi' => null,
                'ko' => '예 빠져들다'
            ],
            [
                'en' => 'Term paper',
                'vi' => null,
                'ko' => '학기 논문'
            ],
            [
                'en' => 'Class Clown',
                'vi' => null,
                'ko' => '반장난꾼'
            ],
            [
                'en' => 'Bunk out',
                'vi' => null,
                'ko' => '수업 땡땡치다'
            ],
            [
                'en' => 'Dormitory',
                'vi' => null,
                'ko' => '기숙사'
            ],
            [
                'en' => 'Grade',
                'vi' => null,
                'ko' => '성적'
            ],
            [
                'en' => 'Come up with',
                'vi' => null,
                'ko' => '생각해 내다'
            ],
            [
                'en' => 'Re-enter school',
                'vi' => null,
                'ko' => '재입학하다'
            ],
            [
                'en' => 'Minor in',
                'vi' => null,
                'ko' => '부전공하다'
            ],
            [
                'en' => 'Major in',
                'vi' => null,
                'ko' => '전공하다'
            ],
            [
                'en' => 'Make-up class',
                'vi' => null,
                'ko' => '보강 수업'
            ],
            [
                'en' => 'Zone out',
                'vi' => null,
                'ko' => '맹해지다, 집중에 잃다'
            ],
            [
                'en' => 'Bookworm',
                'vi' => null,
                'ko' => '모범생'
            ],
            [
                'en' => 'Hang out',
                'vi' => null,
                'ko' => '놀러가다'
            ],
            [
                'en' => 'Clear sb desk',
                'vi' => null,
                'ko' => '책상 정리하다'
            ],
            [
                'en' => 'Lack of foundation',
                'vi' => null,
                'ko' => '기초 부족'
            ],
            [
                'en' => 'Cheat off of',
                'vi' => null,
                'ko' => '시험에 부정행위하다'
            ],
            [
                'en' => 'Pull an all-nighter',
                'vi' => null,
                'ko' => '밤샘공부'
            ],
            [
                'en' => 'Perfect attendance',
                'vi' => null,
                'ko' => '완벽한 출석, 개근'
            ],
            [
                'en' => 'Scholarship',
                'vi' => null,
                'ko' => '장학금'
            ],
            [
                'en' => 'Torn between',
                'vi' => null,
                'ko' => '~ 사이에 갈등하다'
            ],
            [
                'en' => 'Principal',
                'vi' => null,
                'ko' => '교장'
            ],
        ];

        DB::table('vocabularies')->insert($vocabularies);
    }
}
