<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DictController extends Controller
{
    public $baseUrl 	= "http://mazii.net/";
	public $baseUrlApi = "https://api.mazii.net/";

	public $query_word_url;
	public $query_example_url;
	public $query_kanji_url;
	public $google_translate_url;
	public $kindTablesEn = array(
        "MA"=>"martial arts term",
        "X"=>"rude or X-rated term (not displayed in educational software)",
        "abbr"=>"abbreviation",
        "adj-i"=>"adjective (keiyoushi)",
        "adj-ix"=>"adjective (keiyoushi) - yoi/ii class",
        "adj-na"=>"adjectival nouns or quasi-adjectives (keiyodoshi)",
        "adj-no"=>"nouns which may take the genitive case particle `no'",
        "adj-pn"=>"pre-noun adjectival (rentaishi)",
        "adj-t"=>"`taru' adjective",
        "adj-f"=>"noun or verb acting prenominally",
        "adv"=>"adverb (fukushi)",
        "adv-to"=>"adverb taking the `to' particle",
        "arch"=>"archaism",
        "ateji"=>"ateji (phonetic) reading" ,
        "aux"=>"auxiliary" ,
        "aux-v"=>"auxiliary verb" ,
        "aux-adj"=>"auxiliary adjective" ,
        "Buddh"=>"Buddhist term" ,
        "chem"=>"chemistry term" ,
        "chn"=>"children's language" ,
        "col"=>"colloquialism" ,
        "comp"=>"computer terminology" ,
        "conj"=>"conjunction" ,
        "cop-da"=>"copula" ,
        "ctr"=>"counter" ,
        "derog"=>"derogatory" ,
        "eK"=>"exclusively kanji" ,
        "ek"=>"exclusively kana" ,
        "exp"=>"expressions (phrases, clauses, etc.)" ,
        "fam"=>"familiar language" ,
        "fem"=>"female term or language" ,
        "food"=>"food term" ,
        "geom"=>"geometry term",
        "gikun"=>"gikun (meaning as reading) or jukujikun (special kanji reading)",
        "hon"=>"honorific or respectful (sonkeigo) language",
        "hum"=>"humble (kenjougo) language",
        "iK"=>"word containing irregular kanji usage",
        "id"=>"idiomatic expression",
        "ik"=>"word containing irregular kana usage",
        "int"=>"interjection (kandoushi)",
        "io"=>"irregular okurigana usage",
        "iv"=>"irregular verb",
        "ling"=>"linguistics terminology",
        "m-sl"=>"manga slang",
        "male"=>"male term or language",
        "male-sl"=>"male slang",
        "math"=>"mathematics",
        "mil"=>"military",
        "n"=>"noun (common) (futsuumeishi)",
        "n-adv"=>"adverbial noun (fukushitekimeishi)",
        "n-suf"=>"noun, used as a suffix",
        "n-pref"=>"noun, used as a prefix",
        "n-t"=>"noun (temporal) (jisoumeishi)",
        "num"=>"numeric",
        "oK"=>"word containing out-dated kanji",
        "obs"=>"obsolete term",
        "obsc"=>"obscure term",
        "ok"=>"out-dated or obsolete kana usage",
        "oik"=>"old or irregular kana form",
        "on-mim"=>"onomatopoeic or mimetic word",
        "pn"=>"pronoun",
        "poet"=>"poetical term",
        "pol"=>"polite (teineigo) language",
        "pref"=>"prefix",
        "proverb"=>"proverb",
        "prt"=>"particle",
        "physics"=>"physics terminology",
        "rare"=>"rare",
        "sens"=>"sensitive",
        "sl"=>"slang",
        "suf"=>"suffix",
        "uK"=>"word usually written using kanji alone",
        "uk"=>"word usually written using kana alone",
        "unc"=>"unclassified",
        "yoji"=>"yojijukugo",
        "v1"=>"Ichidan verb",
        "v1-s"=>"Ichidan verb - kureru special class",
        "v2a-s"=>"Nidan verb with 'u' ending (archaic)",
        "v4h"=>"Yodan verb with `hu/fu' ending (archaic)",
        "v4r"=>"Yodan verb with `ru' ending (archaic)",
        "v5aru"=>"Godan verb - -aru special class",
        "v5b"=>"Godan verb with `bu' ending",
        "v5g"=>"Godan verb with `gu' ending",
        "v5k"=>"Godan verb with `ku' ending",
        "v5k-s"=>"Godan verb - Iku/Yuku special class",
        "v5m"=>"Godan verb with `mu' ending",
        "v5n"=>"Godan verb with `nu' ending",
        "v5r"=>"Godan verb with `ru' ending",
        "v5r-i"=>"Godan verb with `ru' ending (irregular verb)",
        "v5s"=>"Godan verb with `su' ending",
        "v5t"=>"Godan verb with `tsu' ending",
        "v5u"=>"Godan verb with `u' ending",
        "v5u-s"=>"Godan verb with `u' ending (special class)",
        "v5uru"=>"Godan verb - Uru old class verb (old form of Eru)",
        "vz"=>"Ichidan verb - zuru verb (alternative form of -jiru verbs)",
        "vi"=>"intransitive verb",
        "vk"=>"Kuru verb - special class",
        "vn"=>"irregular nu verb",
        "vr"=>"irregular ru verb, plain form ends with -ri",
        "vs"=>"noun or participle which takes the aux. verb suru",
        "vs-c"=>"su verb - precursor to the modern suru",
        "vs-s"=>"suru verb - special class",
        "vs-i"=>"suru verb - irregular",
        "kyb"=>"Kyoto-ben",
        "osb"=>"Osaka-ben",
        "ksb"=>"Kansai-ben",
        "ktb"=>"Kantou-ben",
        "tsb"=>"Tosa-ben",
        "thb"=>"Touhoku-ben",
        "tsug"=>"Tsugaru-ben",
        "kyu"=>"Kyuushuu-ben",
        "rkb"=>"Ryuukyuu-ben",
        "nab"=>"Nagano-ben",
        "hob"=>"Hokkaido-ben",
        "vt"=>"transitive verb",
        "vulg"=>"vulgar expression or word",
        "adj-kari"=>"`kari' adjective (archaic)",
        "adj-ku"=>"`ku' adjective (archaic)",
        "adj-shiku"=>"`shiku' adjective (archaic)",
        "adj-nari"=>"archaic/formal form of na-adjective",
        "n-pr"=>"proper noun",
        "v-unspec"=>"verb unspecified",
        "v4k"=>"Yodan verb with `ku' ending (archaic)",
        "v4g"=>"Yodan verb with `gu' ending (archaic)",
        "v4s"=>"Yodan verb with `su' ending (archaic)",
        "v4t"=>"Yodan verb with `tsu' ending (archaic)",
        "v4n"=>"Yodan verb with `nu' ending (archaic)",
        "v4b"=>"Yodan verb with `bu' ending (archaic)",
        "v4m"=>"Yodan verb with `mu' ending (archaic)",
        "v2k-k"=>"Nidan verb (upper class) with `ku' ending (archaic)",
        "v2g-k"=>"Nidan verb (upper class) with `gu' ending (archaic)",
        "v2t-k"=>"Nidan verb (upper class) with `tsu' ending (archaic)",
        "v2d-k"=>"Nidan verb (upper class) with `dzu' ending (archaic)",
        "v2h-k"=>"Nidan verb (upper class) with `hu/fu' ending (archaic)",
        "v2b-k"=>"Nidan verb (upper class) with `bu' ending (archaic)",
        "v2m-k"=>"Nidan verb (upper class) with `mu' ending (archaic)",
        "v2y-k"=>"Nidan verb (upper class) with `yu' ending (archaic)",
        "v2r-k"=>"Nidan verb (upper class) with `ru' ending (archaic)",
        "v2k-s"=>"Nidan verb (lower class) with `ku' ending (archaic)",
        "v2g-s"=>"Nidan verb (lower class) with `gu' ending (archaic)",
        "v2s-s"=>"Nidan verb (lower class) with `su' ending (archaic)",
        "v2z-s"=>"Nidan verb (lower class) with `zu' ending (archaic)",
        "v2t-s"=>"Nidan verb (lower class) with `tsu' ending (archaic)",
        "v2d-s"=>"Nidan verb (lower class) with `dzu' ending (archaic)",
        "v2n-s"=>"Nidan verb (lower class) with `nu' ending (archaic)",
        "v2h-s"=>"Nidan verb (lower class) with `hu/fu' ending (archaic)",
        "v2b-s"=>"Nidan verb (lower class) with `bu' ending (archaic)",
        "v2m-s"=>"Nidan verb (lower class) with `mu' ending (archaic)",
        "v2y-s"=>"Nidan verb (lower class) with `yu' ending (archaic)",
        "v2r-s"=>"Nidan verb (lower class) with `ru' ending (archaic)",
        "v2w-s"=>"Nidan verb (lower class) with `u' ending and `we' conjugation (archaic)",
        "archit"=>"architecture term",
        "astron"=>"astronomy, etc. term",
        "baseb"=>"baseball term",
        "biol"=>"biology term",
        "bot"=>"botany term",
        "bus"=>"business term",
        "econ"=>"economics term",
        "engr"=>"engineering term",
        "finc"=>"finance term",
        "geol"=>"geology, etc. term",
        "law"=>"law, etc. term",
        "mahj"=>"mahjong term",
        "med"=>"medicine, etc. term",
        "music"=>"music term",
        "Shinto"=>"Shinto term",
        "shogi"=>"shogi term",
        "sports"=>"sports term",
        "sumo"=>"sumo term",
        "zool"=>"zoology term",
        "joc"=>"jocular, humorous term",
        "anat"=>"anatomical term"

    );
	public $kindTablesVi = array(
		"abbr"=>"từ viết tắt",
        "adj"=>"tính từ",
        "adj-na"=>"tính từ đuôi な",
        "adj-no"=>"danh từ sở hữu cách thêm の",
        "adj-pn"=>"tính từ đứng trước danh từ",
        "adj-s"=>"tính từ đặc biệt",
        "adj-t"=>"tính từ đuổi tara",
        "adv"=> "trạng từ",
        "adv-n"=> "danh từ làm phó từ",
        "adv-to"=> "trạng từ thêm と",
        "arch"=> "từ cổ",
        "ateji"=> "ký tự thay thế",
        "aux"=> "trợ từ",
        "aux-v"=> "trợ động từ",
        "aux-adj"=> "t=>h từ phụ trợ",
        "Buddh"=> "thuật ngữ phật giáo",
        "chn"=> "ngôn ngữ trẻ em",
        "col"=> "thân mật ngữ",
        "comp"=> "thuật ngữ tin học",
        "conj"=> "liên từ",
        "derog"=> "xúc phạm ngữ",
        "ek"=> "hán tự đặc trưng",
        "exp"=> "cụm từ",
        "fam"=> "từ ngữ thân thuộc",
        "fem"=> "phụ nữ hay dùng",
        "food"=> "thuật ngữ thực phẩm",
        "geom"=> "thuật ngữ hình học",
        "gikun"=> "gikun",
        "gram"=> "thuộc về ngữ pháp",
        "hon"=> "tôn kính ngữ",
        "hum"=> "khiêm nhường ngữ",
        "id"=> "thành ngữ",
        "int"=> "thán từ",
        "iK"=> "từ chứa kanji bất quy tắc",
        "ik"=> "từ chứa kana bất quy tắc",
        "io"=> "okurigana bất quy tắc",
        "iv"=> "động từ bất quy tắc",
        "kyb"=> "giọng Kyoto",
        "ksb"=> "giọng Kansai",
        "ktb"=> "giọng Kantou",
        "ling"=>"thuật ngữ ngôn ngữ học",
        "MA"=> "thuật ngữ nghệ thuật",
        "male"=> "tiếng lóng của nam giới",
        "math"=> "thuật ngữ toán học",
        "mil"=> "thuật ngữ quân sự",
        "m-sl"=> "thuật ngữ truyện tranh",
        "n"=> "danh từ",
        "n-adv"=> "danh từ làm phó từ",
        "n-pref"=> "danh từ làm tiền tố",
        "n-suf"=> "danh từ làm hậu tố",
        "n-t"=> "danh từ chỉ thời gian",
        "neg"=> "thể phủ định",
        "neg-v"=> "động từ mang nghĩa phủ định",
        "ng"=> "từ trung tính",
        "obs"=> "từ cổ",
        "obsc"=> "từ tối nghĩa",
        "oK"=> "từ chứa kanji cổ",
        "ok"=> "từ chứa kana cổ",
        "osk"=> "Giọng Osaka",
        "physics"=> "thuật ngữ vật lý",
        "pol"=> "thể lịch sự",
        "pref"=> "tiếp đầu ngữ",
        "prt"=> "giới từ",
        "qv"=> "tham khảo mục khác",
        "rare"=> "từ hiếm gặp",
        "sl"=> "tiếng lóng",
        "suf"=> "hậu tố",
        "tsb"=> "giọng Tosa",
        "uK"=> "từ sử dụng kanji đứng một mình",
        "uk"=> "từ sử dụng kana đứng một mình",
        "v"=> "động từ",
        "v1"=> "động từ nhóm 2",
        "v5"=> "động từ nhóm 1",
        "v5aru"=> "động từ nhóm 1 -aru",
        "v5b"=> "động từ nhóm 1 -bu",
        "v5g"=> "động từ nhóm 1 -ku",
        "v5k"=> "động từ nhóm 1 -ku",
        "v5k-s"=> "động từ nhóm 1 -iku/yuku",
        "v5m"=> "động từ nhóm 1 -mu",
        "v5n"=> "động từ nhóm 1 -nu",
        "v5r"=> "Động từ nhóm 1 -ru",
        "v5r-i"=> "Động từ nhóm 1 bất quy tắc -ru",
        "v5s"=>"động từ nhóm 1 -su",
        "v5t"=> "động từ nhóm 1 -tsu",
        "v5u"=> "động từ nhóm 1 -u",
        "v5u-s"=> "động từ nhóm 1 -u (đặc biệt)",
        "v5uru"=> "động từ nhóm 1 -uru",
        "vi"=> "tự động từ",
        "vk"=> "động từ kuru (đặc biệt)",
        "vs"=> "danh từ hoặc giới từ làm trợ từ cho động từ suru",
        "vs-i"=> "động từ bất quy tắc -suru",
        "vt"=> "tha động từ",
        "vulg"=> "thuật ngữ thô tục",
        "vz"=> "tha động từ",
        "X"=> "thuật ngữ thô tục"
	);
	public $kindTablesCn = array(
		"abbr"=> "缩写",
		"adj"=> "形容词（keiyoushi）", 
		"adj-i"=> "形容词（keiyoushi）", 
		"adj-na"=> "形容词名词或准形容词（keiyodoshi）", 
		"adj-no"=> "名词可能采取属性案例粒子'不'", 
		"adj-pn"=> "名词前形容词（rentaishi）", 
		"adj-s"=> "特殊形容词（例如ookii）", 
		"adj-t"=> "'taru'形容词",
		"adv"=> "副词（fukushi）",
		"adv-n"=> "状语名词", 
		"adv-to"=> "副词取'到'粒子",
		"arch"=> "拟古主义", 
		"ateji"=> "ateji（语音）阅读",
		"aux"=> "辅", "aux-v"=>"助动词", 
		"aux-adj"=> "辅助形容词", 
		"Buddh"=> "佛教术语", 
		"chn"=> "孩子的语言", 
		"col"=> "俗话",
		"comp"=> "计算机术语",
		"conj"=> "连词",
		"derog"=> "贬", 
		"ek"=> "专属假名", 
		"exp"=> "表达（短语，从句等）", 
		"fam"=> "熟悉的语言", 
		"fem"=> "女性用语或语言", 
		"food"=> "食品术语", 
		"geom"=> "几何术语", 
		"gikun"=> "gikun（意思是阅读）或jukujikun（特殊的汉字阅读）", 
		"gram"=> "语法术语", 
		"hon"=> "尊敬或尊重（sonkeigo）语言", 
		"hum"=> "卑微的（kenjougo）语言", 
		"id"=> "惯用语", 
		"int"=> "感叹词（kandoushi）", 
		"iK"=> "含有不规则汉字用法的单词", 
		"ik"=> "含有不规则假名用法的单词", 
		"io"=> "不规则的okurigana用法", 
		"iv"=> "不规则动词", 
		"kyb"=> "京都方言", 
		"ksb"=> "关西奔", 
		"ktb"=> "关东奔", 
		"ling"=> "语言学术语", 
		"MA"=> "武术术语", 
		"male"=> "男性用语或语言", 
		"math"=> "数学", 
		"mil"=> "军事", 
		"m-sl"=> "漫画俚语", 
		"n"=> "名词（普通）（futsuumeishi）", 
		"n-adv"=> "状语名词（fukushitekimeishi）", 
		"n-pref"=> "名词，用作前缀", 
		"n-suf"=> "名词，用作后缀", 
		"n-t"=> "名词（时间）（jisoumeishi）", 
		"neg"=> "否定（用否定句，或用否定动词）", 
		"neg-v"=> "否定动词（与其一起使用时）", 
		"obs"=> "过时的术语", 
		"obsc"=> "模糊的术语", 
		"oK"=> "含有过时汉字的单词", 
		"ok"=> "过时或过时的假名用法", 
		"osk"=> "大阪方言", 
		"physics"=> "物理术语", 
		"pol"=> "礼貌（teineigo）语言", 
		"pref"=> "字首", 
		"prt"=> "粒子", 
		"qv"=> "quod vide（见另一个条目）", 
		"rare"=> "罕见", 
		"sl"=>"俚语", 
		"suf"=> "后缀", 
		"tsb"=> "土佐方言", 
		"uK"=> "通常单独用汉字写的字", 
		"uk"=> "通常单独用假名写的字", 
		"v"=> "动词", 
		"v1"=> "Ichidan动词", 
		"v5aru"=> "Godan动词 -  -aru特殊类", 
		"v5b"=> "带有'bu'结尾的Godan动词", 
		"v5g"=> "带有'gu'结尾的Godan动词", 
		"v5k"=> "带有'ku'结尾的Godan动词", 
		"v5k-s"=> "Godan动词 -  Iku / Yuku特别班", 
		"v5m"=> "带有'mu'结尾的Godan动词", 
		"v5n"=> "带有'nu'结尾的Godan动词", 
		"v5r"=> "带有'ru'结尾的Godan动词", 
		"v5r-i"=> "带有'ru'结尾的Godan动词（不规则动词）", 
		"v5s"=> "带有'su'结尾的Godan动词", 
		"v5t"=> "用'tsu'结尾的Godan动词", 
		"v5u"=> "带有'你'结尾的戈丹动词", 
		"v5u-s"=> "带有'u'结尾的Godan动词（特殊课程）", 
		"v5uru"=> "Godan动词 -  Uru旧类动词（Eru的旧形式）", 
		"vi"=> "不及物动词", 
		"vk"=> "库鲁动词 - 特殊课程", 
		"vs"=> "带有辅助的名词或分词。动词suru", 
		"vs-i"=> "suru动词 - 不规则", 
		"vt"=> "及物动词",
		"vulg"=> "粗俗的表达或言语", 
		"vz"=> "及物动词", 
		"X"=> "粗鲁或X级术语（未在教育软件中显示）"
	);
	public $kindTablesTw = array(
		"abbr"=>"縮寫", 
		"adj"=>"形容詞（keiyoushi）", 
		"adj-i"=>"形容詞（keiyoushi）", 
		"adj-na"=>"形容詞名詞或準形容詞（keiyodoshi）", 
		"adj-no"=>"名詞可能採取屬性案例粒子'不'", 
		"adj-pn"=>"名詞前形容詞（rentaishi）", 
		"adj-s"=>"特殊形容詞（例如ookii）", 
		"adj-t"=>"'taru'形容詞", 
		"adv"=>"副詞（fukushi）", 
		"adv-n"=>"狀語名詞", 
		"adv-to"=>"副詞取'到'粒子", 
		"arch"=>"擬古主義", 
		"ateji"=>"ateji（語音）閱讀", 
		"aux"=>"輔", 
		"aux-v"=>"助動詞", 
		"aux-adj"=>"輔助形容詞", 
		"Buddh"=>"佛教術語", 
		"chn"=>"孩子的語言", 
		"col"=>"俗話", 
		"comp"=>"計算機術語", 
		"conj"=>"連詞", 
		"derog"=>"貶", 
		"ek"=>"專屬假名", 
		"exp"=>"表達（短語，從句等）", 
		"fam"=>"熟悉的語言", 
		"fem"=>"女性用語或語言", 
		"food"=>"食品術語", 
		"geom"=>"幾何術語", 
		"gikun"=>"gikun（意思是閱讀）或jukujikun（特殊的漢字閱讀）", 
		"gram"=>"語法術語", 
		"hon"=>"尊敬或尊重（sonkeigo）語言", 
		"hum"=>"卑微的（kenjougo）語言", 
		"id"=>"慣用語", 
		"int"=>"感嘆詞（kandoushi）", 
		"iK"=>"含有不規則漢字用法的單詞", 
		"ik"=>"含有不規則假名用法的單詞", 
		"io"=>"不規則的okurigana用法", 
		"iv"=>"不規則動詞", 
		"kyb"=>"京都方言", 
		"ksb"=>"關西奔", 
		"ktb"=>"關東奔", 
		"ling"=>"語言學術語", 
		"MA"=>"武術術語", 
		"male"=>"男性用語或語言", 
		"math"=>"數學", 
		"mil"=>"軍事", 
		"m-sl"=>"漫畫俚語", 
		"n"=>"名詞（普通）（futsuumeishi）", 
		"n-adv"=>"狀語名詞（fukushitekimeishi）", 
		"n-pref"=>"名詞，用作前綴", 
		"n-suf"=>"名詞，用作後綴", 
		"n-t"=>"名詞（時間）（jisoumeishi）", 
		"neg"=>"否定（用否定句，或用否定動詞）", 
		"neg-v"=>"否定動詞（與其一起使用時）", 
		"obs"=>"過時的術語", 
		"obsc"=>"模糊的術語", 
		"oK"=>"含有過時漢字的單詞", 
		"ok"=>"過時或過時的假名用法", 
		"osk"=>"大阪方言", 
		"physics"=>"物理術語", 
		"pol"=>"禮貌（teineigo）語言", 
		"pref"=>"字首", 
		"prt"=>"粒子", 
		"qv"=>"quod vide（見另一個條目）", 
		"rare"=>"罕見", 
		"sl"=>"俚語", 
		"suf"=>"後綴", 
		"tsb"=>"土佐方言", 
		"uK"=>"通常單獨用漢字寫的字", 
		"uk"=>"通常單獨用假名寫的字", 
		"v"=>"動詞", 
		"v1"=>"Ichidan動詞", 
		"v5aru"=>"Godan動詞 -  -aru特殊類", 
		"v5b"=>"帶有'bu'結尾的Godan動詞", 
		"v5g"=>"帶有'gu'結尾的Godan動詞", 
		"v5k"=>"帶有'ku'結尾的Godan動詞", 
		"v5k-s"=>"Godan動詞 -  Iku / Yuku特別班", 
		"v5m"=>"帶有'mu'結尾的Godan動詞", 
		"v5n"=>"帶有'nu'結尾的Godan動詞", 
		"v5r"=>"帶有'ru'結尾的Godan動詞", 
		"v5r-i"=>"帶有'ru'結尾的Godan動詞（不規則動詞）", 
		"v5s"=>"帶有'su'結尾的Godan動詞", 
		"v5t"=>"用'tsu'結尾的Godan動詞", 
		"v5u"=>"帶有'你'結尾的戈丹動詞", 
		"v5u-s"=>"帶有'u'結尾的Godan動詞（特殊課程）", 
		"v5uru"=>"Godan動詞 -  Uru舊類動詞（Eru的舊形式）", 
		"vi"=>"不及物動詞", 
		"vk"=>"庫魯動詞 - 特殊課程", 
		"vs"=>"帶有輔助的名詞或分詞。動詞suru", 
		"vs-i"=>"suru動詞 - 不規則", 
		"vt"=>"及物動詞", 
		"vulg"=>"粗俗的表達或言語", 
		"vz"=>"及物動詞", 
		"X"=>"粗魯或X級術語（未在教育軟件中顯示）"
	);
	public function __construct(){
		$this->query_word_url 		= $this->baseUrlApi."api/word/";
		$this->query_example_url 	= $this->baseUrlApi."api/example/";
		$this->query_kanji_url 		= $this->baseUrlApi."api/kanji/";
		$this->google_translate_url = $this->baseUrl."api/gsearch/";
		$this->validate    = new ValidateController();
		$this->kindTableEn = $this->kindTablesEn;
		$this->kindTableVi = $this->kindTablesVi;
		$this->kindTableCn = $this->kindTablesCn;
		$this->kindTableTw = $this->kindTablesTw;
	}

	public function getKind($kind, $dict){
		if($kind == null || $kind == ''){
			return null;
		}
		$result = [];
		$arrKind = explode(', ', $kind);
		
		switch ($dict) {
			case 'jatw':
				$kindTables = $this->kindTableTw;
				break;
			case 'javi':
				$kindTables = $this->kindTableVi;
				break;
			case 'jacn':
				$kindTables = $this->kindTableCn;
				break;
			default:
				$kindTables = $this->kindTableEn;
		}
        // if ($kindTables == null) {
        //     $kindTables = $kind;
        // }
		foreach ($arrKind as $key => $value) {
			$result[] = (isset($kindTables[$value])) ? $kindTables[$value] : $value;
		}

		if(count($result)){
			return $result;
		}
		return null;
	}

// ------------- old --------------
	public $validate;

	

	public function index(Request $request){
		$data['url']	 = urldecode($request->fullUrl());
		$data['welcome'] = '';
		$data['type']	 = 'word';
		$data['search']	 = 'active';
		return view('dictionary.main', $data);
	}

	public function getWord(Request $request, $query, $from, $to){
		$result = json_decode($this->getData('word', $query));
		$kanji  = json_decode($this->getData('kanji', $query));
		
		$wordStatus = $result->status;
		$kanjiStatus = $kanji->status;
		// -----------word-----------
		$suggests = array();
		if($wordStatus == 304){
			$data['noResults'] = '';
		}elseif($wordStatus == 200){
			$listWord = $result->data;
			// check found result for search
			$result = '';
			foreach ($listWord as $key => $value) {
				$phonetic = '';
				if(!empty($value->phonetic)){
					$phonetic = explode(' ', $value->phonetic);
				}

				if($value->word == $query || (is_array($phonetic) && in_array($query, $phonetic))){
					$result = $value;
				}elseif(count($suggests) < 10){
					array_push($suggests, $value);
				}
			}
			if(!empty($result)){
				$data['result'] = $result;
				// convertKind
				foreach ($data['result']->means as $key => $value) {
					if($value->kind != null || $value->kind != ''){
						$value->kind = $this->convertKindToReadable($value->kind);
					}
					if($value->mean != null || $value->mean != ''){
						$value->mean = ucfirst($value->mean);
					}
				}
				$str = $result->word .' '. $result->phonetic .' '. $result->means[0]->mean;
				$data['des'] = $str;
				$data['title'] = $result->word . ' - Mazii Dictionary | Easy Japanese';
			}else{
				$search = $this->googleTranslate($query, $from, $to);
				$search = json_decode($search);
				if(!empty($search)){
					$data['gsearch'] = $search;
				}else{
					$data['noResults'] = '';
				}
			}
		}
		// -----------kanji------------
		if($kanjiStatus == 304){
			$data['noKanji'] = '';
		}elseif($kanjiStatus == 200){
			$res = $kanji->results;
			if($from == 'ja'){
				$kanjis = $this->getKanjiChara($query);
				$data['resultKanji'] = $this->sortHVDataByKeyWord($kanjis, $res);
			}else{
				$data['resultKanji'] = $res;
			}
			// Convert ký tự | thành ,

			foreach ($data['resultKanji'] as $key => $value) {
				$value->mean = str_replace('|', ', ', $value->mean);
				$value->on = str_replace('|', ', ', $value->on);
				$value->kun = str_replace('|', ', ', $value->kun);
			}
		}
		$data['url']		= urldecode($request->fullUrl());
		$data['type'] 		= 'word';
		$data['search'] 	= 'active';
		$data['query'] 		= $query;
		$data['suggests']	= $suggests;
		
		foreach ($data['suggests'] as $key => $value) {
			foreach ($value->means as $k => $val) {
				if($val->kind != null || $val->kind != ''){
					$val->kind = $this->convertKindToReadable($val->kind);
				}
				if($val->mean != null || $val->mean != ''){
					$val->mean = ucfirst($val->mean);
				}
				
			}
		}
		
		return view('dictionary.word', $data);
	}

	public function getKanji(Request $request, $query, $from){
		$query = $query;
		$result = json_decode($this->getData('kanji', $query));
		$status = $result->status;
		if($status == 304){
			$data['noKanji'] = '';
		}elseif($status == 200){
			$res = $result->results;
			if($from == 'ja'){
				$kanjis = $this->getKanjiChara($query);
				$data['result'] = $this->sortHVDataByKeyWord($kanjis, $res);
				if(empty($data['result'])){
					$data['noKanji'] = '';
				}
			}else{
				$data['result'] = $res;
			}
		}
		// Convert ký tự | thành ,
		$des = '';
		$title = '';
		if(isset($data['result'])){
			foreach ($data['result'] as $key => $value) {
				$value->mean = str_replace('|', ', ', $value->mean);
				$value->on = str_replace('|', ', ', $value->on);
				$value->kun = str_replace('|', ', ', $value->kun);
				$des .= $value->kanji . $value->mean;
				$title .= $value->kanji;
			}
			$data['des'] = $des;
			$data['title'] = $title;
		}
		$data['url']	= urldecode($request->fullUrl());
		$data['type'] 	= 'kanji';
		$data['search'] = 'active';
		$data['query']	= $query;
		
		return view('dictionary.kanji', $data);
	}

	public function convertKindToReadable($str){
		$kind = array();
		if(strrpos(',', $str) !== false){
			$kind = explode(',', $str);
			foreach ($kind as $key => $value) {
				$value = trim($value);
			}
		}else{
			$kind[] = $str;
		}

		$result = '';
		foreach ($kind as $key => $value) {
			if(array_key_exists($value, $this->kindTables)){
				$result .= $this->kindTables[$value];
			}else{
				$result .= $value;
			}
			if(count($kind) > 1){
				$result .= ', ';
			}
		}
		return $result;
	}

	public function getKanjiChara($keyword){
		if($keyword == null){
			return '';
		}
		$result = '';
		for ( $i = 0; $i < strlen($keyword); $i++) {
			$c = mb_substr($keyword, $i, 1);
			if ($this->isKanji($c) && stripos($result, $c) === false){
				$result .= $c;
			}
		}
		return $result;
	}

	public function sortHVDataByKeyWord($kw, $data){
		$newData = array();
		for ($i=0; $i < strlen($kw); $i++) { 
			$c = mb_substr($kw, $i, 1);
			foreach ($data as $key => $value) {
				if($c == $value->kanji){
					$exist = false;
					foreach ($newData as $k => $val) {
						if($val->kanji == $c){
							$exist = true;
							break;
						}
					}
					if(!$exist){
						$newData[] = $value;
					}
				}
			}
		}

		return $newData;
	}

	public function isKanji($c){
		if ($c == '々') {      
			return true;        
		}        
		$charcode = $this->uniord($c);       
		if ($charcode >= 0x4E00 && $charcode <= 0x9FBF) {            
			return true;        
		}
		return false;  
	}

	public function uniord($str){
		$k = mb_convert_encoding($str, 'UCS-2LE', 'UTF-8');
		$k1 = ord(substr($k, 0, 1));
		$k2 = ord(substr($k, 1, 1));
		return $k2*256 +$k1;
	}
	
	public function getSentence(Request $request, $query){
		$result = json_decode($this->getData('example', $query));
		$status = $result->status;
		if($status == 304){
			$data['data'] = '';
		}elseif($status == 200){
			$arr = $result->results;
			foreach ($arr as $key => $value) {
				$value->mean = $this->changeMean($value->mean);
			}
			$data['result'] = $arr;
			$data['des'] = $this->validate->replaceTagHTML($arr[0]->mean);
		}
		$data['url'] 	= urldecode($request->fullUrl());
		$data['type'] 	= 'example';
		$data['search'] = 'active';
		$data['query']	= $query;

		return view('dictionary.sentence', $data);
	}

	public function changeMean($mean){
		$html 	 = '';
		$pos 	 = '';
		$writing = '';
		$reading = '';

		$json = json_encode($mean);
		$jap  = json_decode(str_replace('\t', '*', $json));
		$ref  = explode('*', $jap);

		foreach ($ref as $key => $value) {
			$ref1 = explode(' ', $value);
			switch (count($ref1)) {
				case 1:
					$writing = $ref1[0];
					break;
				case 2:
					$writing = $ref1[0];
					$pos = $ref1[1];
					break;
				default:
					$writing = $ref1[0];
					$pos = $ref1[1];
					$reading = $ref1[2];
					break;
			}
			$word = $writing;
			if($pos != null){
				if($reading !== null || $reading !== ''){
					$word = '<ruby><rb>'.$writing.'</rb><rt>'.$reading.'</rt></ruby>';
				}
			}
			$html .= $word;
		}
		return $html;
	}

	public function googleTranslate($query, $from, $to){
		$query = str_replace(' ', '%20', $query);
		$url = $this->google_translate_url.$query."/".$from."/".$to;
		$postData = file_get_contents($url);
		$result	  = json_decode($postData);
		$data = '';
		if($result->status == 200){
			$data = $result->data;
		}
		return $data;
	}
	
	public function getData($type, $query){
		$url = '';
		switch ($type) {
			case 'word':
				$url = $this->query_word_url.$query;
				break;
			case 'example':
				$url = $this->query_example_url.$query;
				break;
			case 'kanji':
				$url = $this->query_kanji_url.$query.'/10';
				break;
			// case 'grammar':
			// 	$url = $this->query_grammar_url.$query;
			// 	break;
			// case 'grammar_detail':
			// 	$url = $this->query_grammar_detail_url.$query;
			// 	break;
		}
		
		$postData = $this->postData($url);
		dd($postData);
		return $postData;
	}

	public function postData($url){
		$ch   = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/x-www-form-urlencoded'
			));

		$response =curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response);
		return $response;
	}

	public function googleTranslateApi($query, $from, $to){
		$query = str_replace(' ', '%20', $query);
		$url = $this->google_translate_url.$query."/".$from."/".$to;
		$postData = file_get_contents($url);
		
		return $postData;
	}
}
