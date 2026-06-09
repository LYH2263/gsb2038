<?php

namespace Database\Seeders;

use App\Models\TeaType;
use Illuminate\Database\Seeder;

class TeaTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => '绿茶',
                'slug' => 'green',
                'description' => '绿茶为不发酵茶，经杀青、揉捻、干燥制成，最大程度保留鲜叶的天然物质。成品绿汤绿叶，清香鲜爽，茶多酚与氨基酸含量高，抗氧化性强。主产区有浙江、安徽、江苏、四川、湖北等。代表名茶：西湖龙井、碧螺春、黄山毛峰、六安瓜片、信阳毛尖等。冲泡宜用 80—85℃ 水温，避免闷泡过久。',
                'image_url' => '/images/tea-types/green.jpg',
            ],
            [
                'name' => '红茶',
                'slug' => 'black',
                'description' => '红茶为全发酵茶，工艺为萎凋、揉捻、发酵、干燥，发酵使茶多酚氧化为茶黄素、茶红素，形成红汤红叶、滋味甜醇的特点。起源于福建，后传至祁门、云南、印度、斯里兰卡等地。代表名茶：祁门红茶、正山小种、滇红、金骏眉等。祁门红茶与印度大吉岭、斯里兰卡乌瓦并称世界三大高香红茶。性温，适合秋冬及体寒者，可清饮或调饮。',
                'image_url' => '/images/tea-types/black.jpg',
            ],
            [
                'name' => '乌龙茶',
                'slug' => 'oolong',
                'description' => '乌龙茶为半发酵茶，介于绿茶与红茶之间，核心工艺为做青（摇青），使叶缘破损氧化、形成「绿叶红镶边」。香气馥郁，花果香明显，滋味醇厚回甘。主产区福建、广东、台湾。闽南以铁观音为代表，闽北以大红袍、水仙为代表，广东以凤凰单丛著称，台湾有冻顶乌龙、东方美人等。冲泡多用沸水、小壶小杯，讲究「七泡有余香」。',
                'image_url' => '/images/tea-types/oolong.jpg',
            ],
            [
                'name' => '白茶',
                'slug' => 'white',
                'description' => '白茶为微发酵茶，工艺最简：萎凋、干燥，不炒不揉，最大程度保留茶毫与天然风味。成品满披白毫、汤色浅黄、毫香蜜韵。主产区福建福鼎、政和、建阳等地。代表品类：白毫银针（全芽）、白牡丹（一芽一二叶）、贡眉、寿眉。性凉，清热润燥，夏季饮用尤宜。福鼎白茶制作技艺等已列入人类非物质文化遗产。',
                'image_url' => '/images/tea-types/white.jpg',
            ],
            [
                'name' => '黄茶',
                'slug' => 'yellow',
                'description' => '黄茶为轻发酵茶，在绿茶工艺基础上增加「闷黄」工序，使叶色变黄、形成黄汤黄叶、香醇甘醇的品质。主产区湖南、四川、安徽、湖北等。代表名茶：君山银针（湖南岳阳）、蒙顶黄芽（四川雅安）、霍山黄芽（安徽）、平阳黄汤等。君山银针唐代即为贡茶，冲泡时芽尖竖立、三起三落，观赏性极佳。产量较少，属小众名茶。',
                'image_url' => '/images/tea-types/yellow.jpg',
            ],
            [
                'name' => '黑茶',
                'slug' => 'dark',
                'description' => '黑茶为后发酵茶，经杀青、揉捻、渥堆、干燥制成，渥堆是关键，通过微生物发酵形成黑褐色泽与陈香醇和口感。主产区湖南、云南、四川、广西、湖北等。代表品类：云南普洱茶、湖南安化黑茶、广西六堡茶、四川藏茶等。普洱茶「越陈越香」，安化千两茶、茯砖等制作技艺已列入人类非遗。性温，消食解腻，适合饭后及高脂饮食后饮用。',
                'image_url' => '/images/tea-types/dark.jpg',
            ],
        ];
        foreach ($types as $t) {
            TeaType::updateOrCreate(['slug' => $t['slug']], $t);
        }
    }
}
