<div class="setting-item">
    <a class="sub-hira bg-color" href="#">
        <span class="bg-color">Chế độ học</span>
    </a>
    <select class="sl-custom select-type">
        <option value="hsk">简体中文</option>
        <option value="tocfl">繁體漢字</option>
    </select>
</div>
<div class="setting-item">
    <a class="sub-hira bg-color" href="#">
        <span class="bg-color">{{ trans('label.list_setting.pinyin') }}</span>
    </a>
    <select class="sl-custom select-hsk">
        <option value="1">HSK1</option>
        <option value="2">HSK2</option>
        <option value="3">HSK3</option>
        <option value="4">HSK4</option>
        <option value="5">HSK5</option>
        <option value="6">HSK6</option>
        <option value="none">{{ trans('label.hidden') }}</option>
    </select>
    <select class="sl-custom select-tocfl">
        <option value="1">TOCFL1</option>
        <option value="2">TOCFL2</option>
        <option value="3">TOCFL3</option>
        <option value="4">TOCFL4</option>
        <option value="5">TOCFL5</option>
        <option value="6">TOCFL6</option>
        <option value="none">{{ trans('label.hidden') }}</option>
    </select>
</div>
<div class="setting-item">
    <a class="bg-color" href="#">
        <span class="bg-color-underline setting-underline">{{ trans('label.list_setting.underline') }}</span>
    </a>
    <select class="sl-custom select-hsk-underline">
        <option value="1">HSK1</option>
        <option value="2">HSK2</option>
        <option value="3">HSK3</option>
        <option value="4">HSK4</option>
        <option value="5">HSK5</option>
        <option value="6">HSK6</option>
        <option value="none">{{ trans('label.hidden') }}</option>
    </select>
    <select class="sl-custom select-tocfl-underline">
        <option value="1">TOCFL1</option>
        <option value="2">TOCFL2</option>
        <option value="3">TOCFL3</option>
        <option value="4">TOCFL4</option>
        <option value="5">TOCFL5</option>
        <option value="6">TOCFL6</option>
        <option value="none">{{ trans('label.hidden') }}</option>
    </select>
</div>
<div id="language" class="setting-item">
    <a class="bg-color">
        <span class="bg-color setting-language">{{ trans('label.list_setting.language') }}</span>
    </a>
    <select class="sl-custom select-show-language">
        <option value="en-US" title="English">English</option>
        <option value="ko-KR" title="Korea">Korea</option>
        <option value="vi-VN" title="Vietnamese">Vietnamese</option>
        <option value="zh-CN" title="Chinese (Simplified)">Chinese (Simplified)</option> 
        <option value="zh-TW" title="Chinese (Traditional)">Chinese (Traditional)</option>
    </select>
</div>
<div class="setting-item">
    <a class="bg-color"  href="#">
        <span class="bg-color setting-size">{{ trans('label.list_setting.size') }}</span>
    </a>
    <select class="size-options sl-custom">
        <option value="50" class="size">12</option>
        <option value="60" class="size">13</option>
        <option value="70" class="size">14</option>
        <option value="80" class="size">15</option>
        <option value="90" class="size">16</option>
        <option value="95" class="size">17</option>
    </select>
</div>