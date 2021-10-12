<?php declare(strict_types=1);

/**
 * Classe WebPage permettant de ne plus écrire l'enrobage HTML lors de la création d'une page Web.
 **/
class WebPage
{
    /**
     * Texte compris entre \<head\> et \</head\>.
     *
     * @var string $head
     */
    private $head = '';

    /**
     * Texte compris entre \<title\> et \</title\>.
     *
     * @var string $title
     */
    private $title = null;

    /**
     * Texte compris entre \<body\> et \</body\>.
     *
     * @var string $body
     */
    private $body = '';

    /**
     * Constructeur.
     *
     * @param string $title Titre de la page
     */
    public function __construct(string $title = null)
    {
        if (!is_null($title)) {
            $this->setTitle($title);
        }
    }

    /**
     * Retourner le contenu de $this->body.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Retourner le contenu de $this->head.
     *
     * @return string
     */
    public function head(): string
    {
        return $this->head;
    }

    /**
     * Affecter le titre de la page.
     *
     * @param string $title Le titre
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Ajouter un contenu dans $this->head.
     *
     * @param string $content Le contenu à ajouter
     */
    public function appendToHead(string $content): void
    {
        $this->head .= $content;
    }

    /**
     * Ajouter un contenu CSS dans head.
     *
     * @param string $css Le contenu CSS à ajouter
     *@see WebPage::appendToHead(string $content) : void
     *
     */
    public function appendCss(string $css): void
    {
        $this->appendToHead(<<<HTML
    <style type='text/css'>
    {$css}
    </style>

HTML
        );
    }

    /**
     * Ajouter l'URL d'un script CSS dans head.
     *
     * @param string $url L'URL du script CSS
     *@see WebPage::appendToHead(string $content) : void
     *
     */
    public function appendCssUrl(string $url): void
    {
        $this->appendToHead(<<<HTML
    <link rel="stylesheet" type="text/css" href="{$url}">

HTML
        );
    }

    /**
     * Ajouter un contenu JavaScript dans head.
     *
     * @param string $js Le contenu JavaScript à ajouter
     *@see WebPage::appendToHead(string $content) : void
     *
     */
    public function appendJs(string $js): void
    {
        $this->appendToHead(<<<HTML
    <script type='text/javascript'>
    {$js}
    </script>

HTML
        );
    }

    /**
     * Ajouter l'URL d'un script JavaScript dans head.
     *
     * @param string $url L'URL du script JavaScript
     *@see WebPage::appendToHead(string $content) : void
     *
     */
    public function appendJsUrl(string $url): void
    {
        $this->appendToHead(<<<HTML
    <script type='text/javascript' src='{$url}'></script>

HTML
        );
    }

    /**
     * Ajouter un contenu dans body.
     *
     * @param string $content Le contenu à ajouter
     */
    public function appendContent(string $content): void
    {
        $this->body .= $content;
    }

    /**
     * Produire la page Web complète.
     *
     * @return string
     *
     * @throws Exception si title n'est pas défini
     */
    public function toHTML(): string
    {
        if (is_null($this->title)) {
            throw new Exception(__CLASS__.': title not set');
        }

        return <<<HTML
        <!doctype html>
        <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" type="text/css" href="css/css.css">
                <title>{$this->title}</title>
                {$this->head()}
            </head>
            <body>
                {$this->getHeader()}
                {$this->body}
                {$this->getFooter()}
            </body>
        </html>
        HTML;
    }

    /**
     * Protéger les caractères spéciaux pouvant dégrader la page Web.
     *
     * @param string $string La chaîne à protéger
     *
     * @return string La chaîne protégée
     *
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function escapeString(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'utf-8');
    }

    public function getHTMLButton(bool $submitType, string $value, string $href=''): string {
        return $submitType ? "<input class='button' type=\"submit\" value=\"$value\">" : "<a class='button' href=\"$href\">$value</a>";
    }

    public function getHTMLInput(string $title = '', string $inputType = '', string $name='', string $id='', string $placeholder='', string $value='', bool $required=true, bool $hidden=false): string {
        return "<div class='form-input'><label for='$id'>$title</label>
        <input type='$inputType' name='$name' id='$id' value='$value' placeholder='$placeholder' ".($required ? "required ": "").($hidden ? "hidden " : "")."></div>";
    }

    public function getSVGPers(): string {
        return "<svg class='pr-1' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M12 3C9.59489 3 7.64516 4.93577 7.64516 7.32367C7.64516 9.71157 9.59489 11.6473 12 11.6473C14.4051 11.6473 16.3548 9.71157 16.3548 7.32367C16.3548 4.93577 14.4051 3 12 3Z' fill='#373737'/>
        <path d='M7.35484 13.9533C4.94973 13.9533 3 15.8891 3 18.277V19.647C3 20.5155 3.63393 21.256 4.49722 21.3959C9.46618 22.2014 14.5338 22.2014 19.5028 21.3959C20.3661 21.256 21 20.5155 21 19.647V18.277C21 15.8891 19.0503 13.9533 16.6452 13.9533H16.2493C16.0351 13.9533 15.8222 13.9869 15.6185 14.053L14.6134 14.3788C12.9152 14.9293 11.0848 14.9293 9.38662 14.3788L8.3815 14.053C8.17784 13.9869 7.96494 13.9533 7.75069 13.9533H7.35484Z' fill='#373737'/>
        </svg>";
    }

    public function getSVGMail():string {
        return "<svg class='pr-1' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M5.33333 6C4.44928 6 3.60143 6.33714 2.97631 6.93726C2.35119 7.53737 2 8.35131 2 9.2V9.5216L12 14.6912L22 9.5232V9.2C22 8.35131 21.6488 7.53737 21.0237 6.93726C20.3986 6.33714 19.5507 6 18.6667 6H5.33333Z' fill='#373737'/>
        <path d='M22 11.3392L12.395 16.304C12.2736 16.3667 12.1379 16.3996 12 16.3996C11.8621 16.3996 11.7264 16.3667 11.605 16.304L2 11.3392V18.8C2 19.6487 2.35119 20.4626 2.97631 21.0627C3.60143 21.6628 4.44928 22 5.33333 22H18.6667C19.5507 22 20.3986 21.6628 21.0237 21.0627C21.6488 20.4626 22 19.6487 22 18.8V11.3392Z' fill='#373737'/>
        </svg>";
    }

    public function getSVGMdp():string {
        return "<svg class='pr-1' width='16' height='22' viewBox='0 0 16 22' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path fill-rule='evenodd' clip-rule='evenodd' d='M2.80757 6.31892L3.18707 9.87854L2.4191 9.94244C1.34816 10.0315 0.473688 10.8705 0.300085 11.9755C-0.100028 14.5221 -0.100028 17.1196 0.300085 19.6662C0.473688 20.7712 1.34816 21.6101 2.4191 21.6992L4.07416 21.8369C6.68723 22.0544 9.31277 22.0544 11.9258 21.8369L13.5809 21.6992C14.6518 21.6101 15.5263 20.7712 15.6999 19.6662C16.1 17.1196 16.1 14.5221 15.6999 11.9755C15.5263 10.8705 14.6518 10.0315 13.5809 9.94244L12.8128 9.87853L13.1923 6.31892C13.2371 5.89929 13.2371 5.47579 13.1923 5.05616L13.1671 4.81994C12.9 2.31426 11.0099 0.331681 8.6074 0.0371121C8.20382 -0.0123707 7.79607 -0.0123707 7.39249 0.037112C4.99 0.33168 3.09989 2.31426 2.83275 4.81994L2.80757 5.05616C2.76283 5.47579 2.76283 5.89929 2.80757 6.31892ZM8.41352 1.7546C8.13875 1.72091 7.86114 1.72091 7.58637 1.7546C5.95067 1.95516 4.66382 3.30496 4.48194 5.01091L4.45676 5.24714C4.42555 5.53984 4.42555 5.83524 4.45676 6.12794L4.84261 9.74709C6.94534 9.60655 9.05455 9.60654 11.1573 9.74708L11.5431 6.12794C11.5743 5.83524 11.5743 5.53984 11.5431 5.24714L11.5179 5.01091C11.3361 3.30496 10.0492 1.95516 8.41352 1.7546ZM7.99997 14.0915C7.08354 14.0915 6.34063 14.8658 6.34063 15.8208C6.34063 16.7759 7.08354 17.5502 7.99997 17.5502C8.9164 17.5502 9.65931 16.7759 9.65931 15.8208C9.65931 14.8658 8.9164 14.0915 7.99997 14.0915Z' fill='#373737'/>
        </svg>";
    }

    public function getSVGTel(): string {
        return "<svg class='pr-1' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
        <path d='M15.75 2C16.3467 2 16.919 2.23705 17.341 2.65901C17.7629 3.08097 18 3.65326 18 4.25V19.75C18 20.3467 17.7629 20.919 17.341 21.341C16.919 21.7629 16.3467 22 15.75 22H8.25C7.65326 22 7.08097 21.7629 6.65901 21.341C6.23705 20.919 6 20.3467 6 19.75V4.25C6 3.65326 6.23705 3.08097 6.65901 2.65901C7.08097 2.23705 7.65326 2 8.25 2H15.75ZM14.75 4.5H9.25C9.05998 4.50006 8.87706 4.57224 8.73821 4.70197C8.59936 4.8317 8.51493 5.0093 8.50197 5.19888C8.48902 5.38846 8.54852 5.57589 8.66843 5.7233C8.78835 5.87071 8.95975 5.9671 9.148 5.993L9.25 6H14.75C14.94 5.99994 15.1229 5.92776 15.2618 5.79803C15.4006 5.6683 15.4851 5.4907 15.498 5.30112C15.511 5.11154 15.4515 4.92411 15.3316 4.7767C15.2117 4.62929 15.0402 4.5329 14.852 4.507L14.75 4.5Z' fill='#373737'/>
        </svg>";
    }

    public function getSVGInsta(): string {
        return "<svg width='25' height='24' viewBox='0 0 25 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <g clip-path='url(#clip0)'>
                        <path d='M12.4999 8.75005C10.705 8.75005 9.24994 10.2051 9.24994 12C9.24994 13.795 10.705 15.25 12.4999 15.25C14.2949 15.25 15.7499 13.795 15.7499 12C15.7499 10.2051 14.2949 8.75005 12.4999 8.75005Z' fill='#E2E2E2'/>
                        <path fill-rule='evenodd' clip-rule='evenodd' d='M7.26948 3.08151C10.7176 2.69614 14.2823 2.69614 17.7304 3.08151C19.6288 3.29369 21.16 4.78947 21.3828 6.69452C21.7951 10.2195 21.7951 13.7806 21.3828 17.3056C21.16 19.2106 19.6288 20.7064 17.7304 20.9186C14.2823 21.304 10.7176 21.304 7.26948 20.9186C5.37108 20.7064 3.83989 19.2106 3.61707 17.3056C3.20479 13.7806 3.20479 10.2195 3.61707 6.69452C3.83989 4.78947 5.37108 3.29369 7.26948 3.08151ZM17.4999 6.00005C16.9477 6.00005 16.4999 6.44776 16.4999 7.00005C16.4999 7.55233 16.9477 8.00005 17.4999 8.00005C18.0522 8.00005 18.4999 7.55233 18.4999 7.00005C18.4999 6.44776 18.0522 6.00005 17.4999 6.00005ZM7.74994 12C7.74994 9.37669 9.87659 7.25005 12.4999 7.25005C15.1233 7.25005 17.2499 9.37669 17.2499 12C17.2499 14.6234 15.1233 16.75 12.4999 16.75C9.87659 16.75 7.74994 14.6234 7.74994 12Z' fill='#E2E2E2'/>
                    </g>
                    <defs>
                        <clipPath id='clip0''>
                            <rect width='24' height='24' fill='white' transform='translate(0.5)'/>
                        </clipPath>
                    </defs>
                </svg>";
    }

    public function getSVGYoutube(): string {
        return "<svg width='25' height='24' viewBox='0 0 25 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path fill-rule='evenodd' clip-rule='evenodd' d='M7.48901 4.89055C10.8247 4.62958 14.1756 4.62958 17.5113 4.89055L19.752 5.06585C21.0001 5.16349 22.0211 6.09889 22.2274 7.33366C22.7436 10.4232 22.7436 13.5769 22.2274 16.6664C22.0211 17.9012 21.0001 18.8366 19.752 18.9342L17.5113 19.1095C14.1756 19.3705 10.8247 19.3705 7.489 19.1095L5.2483 18.9342C4.00023 18.8366 2.97921 17.9012 2.7729 16.6664C2.25669 13.5769 2.25669 10.4232 2.7729 7.33366C2.97921 6.09889 4.00023 5.16349 5.2483 5.06585L7.48901 4.89055ZM10.5001 14.4702V9.52989C10.5001 9.2967 10.7545 9.15267 10.9545 9.27264L15.0714 11.7428C15.2656 11.8593 15.2656 12.1408 15.0714 12.2573L10.9545 14.7274C10.7545 14.8474 10.5001 14.7034 10.5001 14.4702Z' fill='#E2E2E2'/>
                </svg>";
    }

    public function getSVGTwitter(): string {
        return "<svg width='25' height='24' viewBox='0 0 25 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M22.3098 6.22724C22.3677 6.14647 22.2821 6.04218 22.1894 6.07811C21.5307 6.33344 20.8423 6.50571 20.14 6.5906C20.9219 6.12348 21.5253 5.4124 21.8599 4.57053C21.894 4.48477 21.8003 4.40819 21.7195 4.4528C20.9928 4.85414 20.2091 5.14313 19.3949 5.30957C19.3608 5.31654 19.3257 5.30494 19.3018 5.27969C18.6908 4.63502 17.8866 4.20578 17.0098 4.05745C16.1147 3.90601 15.1946 4.05596 14.3938 4.48379C13.5931 4.91162 12.957 5.59313 12.5853 6.42144C12.2319 7.209 12.1365 8.08691 12.3108 8.93003C12.3243 8.99545 12.2731 9.05721 12.2065 9.05307C10.6224 8.95469 9.07469 8.53336 7.65868 7.81446C6.24627 7.09739 4.99554 6.09983 3.98267 4.8833C3.93772 4.82931 3.85252 4.83629 3.81977 4.89845C3.5046 5.49651 3.33958 6.16311 3.34003 6.8406C3.33875 7.51498 3.50425 8.17922 3.82178 8.77416C4.13932 9.36911 4.59905 9.87631 5.16003 10.2506C4.5651 10.2344 3.98178 10.0873 3.45128 9.82067C3.38291 9.78631 3.3009 9.83524 3.30446 9.91167C3.34583 10.8009 3.67228 11.6936 4.23734 12.3786C4.83866 13.1074 5.67328 13.6062 6.60003 13.7906C6.24328 13.8992 5.8729 13.9564 5.50003 13.9606C5.29709 13.9582 5.09454 13.9433 4.89356 13.9158C4.81904 13.9056 4.7582 13.9764 4.78428 14.0469C5.0607 14.7944 5.54903 15.4467 6.1911 15.9228C6.87755 16.4318 7.7056 16.7142 8.56003 16.7306C7.11723 17.8659 5.33591 18.4855 3.50003 18.4906C3.31138 18.4912 3.12282 18.4851 2.93471 18.4723C2.8287 18.4651 2.77995 18.6063 2.87132 18.6605C4.66009 19.7221 6.70462 20.2831 8.79003 20.2806C10.3297 20.2966 11.8572 20.0056 13.2831 19.4247C14.7091 18.8437 16.0051 17.9845 17.0952 16.8971C18.1854 15.8097 19.048 14.516 19.6326 13.0915C20.2172 11.667 20.5121 10.1403 20.5 8.6006V8.12077C20.5 8.0892 20.515 8.05951 20.5402 8.04048C21.2184 7.52834 21.8149 6.91691 22.3098 6.22724Z' fill='#E2E2E2'/>
                </svg>";
    }

    public function getSVGMiniLogo(): string {
        return "<svg width='43' height='44' viewBox='0 0 43 44' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path d='M12.9534 39.7074H10.3454C9.02759 39.708 7.73832 39.3233 6.63625 38.6007C5.53418 37.8781 4.66739 36.8491 4.14253 35.6402C3.61766 34.4314 3.45762 33.0955 3.68209 31.7969C3.90656 30.4984 4.50575 29.2937 5.40594 28.3312L7.44573 26.143C8.43059 25.0876 8.96717 23.691 8.94234 22.2477C8.91751 20.8043 8.33322 19.427 7.31264 18.4061L5.30869 16.4022C5.07559 16.1608 4.9466 15.8376 4.94952 15.5021C4.95243 15.1665 5.08701 14.8456 5.32428 14.6083C5.56154 14.3711 5.88249 14.2365 6.21801 14.2336C6.55354 14.2307 6.87678 14.3596 7.11813 14.5927L9.11953 16.5967C10.609 18.0867 11.4617 20.0968 11.498 22.2033C11.5343 24.3098 10.7513 26.348 9.31404 27.8884L7.27425 30.0767C6.7164 30.6754 6.34539 31.4239 6.20668 32.2304C6.06798 33.0369 6.16762 33.8664 6.49338 34.6171C6.81915 35.3678 7.35689 36.0071 8.04066 36.4567C8.72444 36.9063 9.52455 37.1466 10.3429 37.1481H11.6302C11.6251 36.626 11.6277 35.9708 11.6507 35.2235C11.7172 33.2784 11.9322 30.6602 12.5465 28.0215C13.1607 25.3982 14.1896 22.6521 15.9504 20.5381C17.5192 18.6544 19.6512 17.3005 22.4562 16.9857V9.47152C22.4587 6.38497 24.9643 3.87683 28.0534 3.87683C29.3536 3.87683 30.408 4.93383 30.408 6.23653V7.47269H33.4203C35.1095 7.47269 36.6733 8.34542 37.5639 9.7812L38.6772 11.5778C40.6249 14.7233 38.5032 18.7542 34.9022 19.0127V35.7302C34.9022 37.9261 33.1235 39.7074 30.9276 39.7074H29.5123V35.7302C29.5126 34.8538 29.3403 33.9859 29.0051 33.1761C28.6699 32.3663 28.1785 31.6305 27.5589 31.0106C26.9393 30.3908 26.2037 29.8991 25.394 29.5636C24.5843 29.2282 23.7165 29.0555 22.8401 29.0555H20.593C20.2536 29.0555 19.9281 29.1903 19.6881 29.4303C19.4482 29.6703 19.3133 29.9958 19.3133 30.3352C19.3133 30.6746 19.4482 31 19.6881 31.24C19.9281 31.48 20.2536 31.6148 20.593 31.6148H22.8401C25.1102 31.6148 26.9529 33.4575 26.9529 35.7302V39.7074H12.9534Z' fill='#02897A'/>
                </svg>";

    }

    private function getHeader() : string {
        return <<<HTML
        <nav class="d-flex justify-content-between mb-5">
            <svg class="mr-auto p-3" width="390" height="82" viewBox="0 0 390 82" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.2804 81.375H15.3575C12.3645 81.3763 9.43645 80.5026 6.93354 78.8615C4.43062 77.2204 2.46205 74.8834 1.27003 72.138C0.0780081 69.3927 -0.285465 66.3587 0.224331 63.4095C0.734126 60.4602 2.09495 57.7244 4.13938 55.5384L8.77194 50.5687C11.0087 48.1718 12.2273 45 12.1709 41.7221C12.1145 38.4441 10.7875 35.3161 8.46969 32.9976L3.9185 28.4464C3.38911 27.8983 3.09617 27.1641 3.10279 26.4021C3.10942 25.6401 3.41506 24.9112 3.95391 24.3723C4.49275 23.8335 5.22167 23.5278 5.98368 23.5212C6.74569 23.5146 7.47982 23.8075 8.02794 24.3369L12.5733 28.8881C15.956 32.2721 17.8927 36.8372 17.9751 41.6213C18.0575 46.4053 16.2792 51.0344 13.0151 54.5329L8.3825 59.5026C7.11557 60.8624 6.27295 62.5623 5.95794 64.3939C5.64293 66.2256 5.86922 68.1093 6.60907 69.8142C7.34892 71.5192 8.57018 72.9712 10.1231 73.9922C11.676 75.0133 13.4932 75.559 15.3517 75.5625H18.2754C18.2638 74.3767 18.2696 72.8887 18.3219 71.1915C18.473 66.774 18.9613 60.8278 20.3563 54.8351C21.7513 48.8773 24.0879 42.6405 28.0869 37.8394C31.6499 33.5614 36.4918 30.4866 42.8623 29.7716V12.7061C42.8681 5.69625 48.5585 0 55.5742 0C58.5269 0 60.9217 2.40056 60.9217 5.35913V8.16656H67.763C71.5993 8.16656 75.1507 10.1486 77.1734 13.4094L79.7019 17.4898C84.1252 24.6334 79.3066 33.7881 71.1284 34.3751V72.3424C71.1284 77.3295 67.0888 81.375 62.1016 81.375H58.8873V72.3424C58.8881 70.3519 58.4967 68.3809 57.7355 66.5417C56.9743 64.7026 55.8583 63.0314 54.4511 61.6237C53.0439 60.216 51.3732 59.0993 49.5343 58.3374C47.6955 57.5755 45.7246 57.1834 43.7341 57.1834H38.6308C37.86 57.1834 37.1208 57.4896 36.5757 58.0346C36.0307 58.5796 35.7245 59.3188 35.7245 60.0896C35.7245 60.8604 36.0307 61.5996 36.5757 62.1447C37.1208 62.6897 37.86 62.9959 38.6308 62.9959H43.7341C48.8898 62.9959 53.0748 67.1809 53.0748 72.3424V81.375H21.2804Z" fill="#02897A"/>
                <path d="M132.009 62.6875C130.643 62.6875 129.769 62.0475 129.385 60.7675L116.329 19.8075L116.201 19.2315C116.201 18.8902 116.329 18.5915 116.585 18.3355C116.883 18.0368 117.225 17.8875 117.609 17.8875H125.545C126.142 17.8875 126.633 18.0582 127.017 18.3995C127.443 18.7408 127.721 19.1248 127.849 19.5515L136.937 49.1195L145.961 19.5515C146.089 19.1248 146.345 18.7408 146.729 18.3995C147.155 18.0582 147.667 17.8875 148.265 17.8875H156.265C156.606 17.8875 156.905 18.0368 157.161 18.3355C157.459 18.5915 157.609 18.8902 157.609 19.2315L157.481 19.8075L144.425 60.7675C144.041 62.0475 143.166 62.6875 141.801 62.6875H132.009ZM176.082 63.3275C170.962 63.3275 166.93 61.9408 163.986 59.1675C161.042 56.3942 159.506 52.3622 159.378 47.0715V44.8315C159.549 39.7968 161.106 35.8715 164.05 33.0555C167.037 30.1968 171.026 28.7675 176.018 28.7675C179.645 28.7675 182.695 29.5142 185.17 31.0075C187.687 32.4582 189.565 34.4635 190.802 37.0235C192.082 39.5835 192.722 42.5275 192.722 45.8555V47.3915C192.722 47.8182 192.573 48.2022 192.274 48.5435C191.975 48.8422 191.591 48.9915 191.122 48.9915H170.642V49.4395C170.727 51.4448 171.218 53.0662 172.114 54.3035C173.01 55.5408 174.311 56.1595 176.018 56.1595C177.085 56.1595 177.959 55.9462 178.642 55.5195C179.325 55.0502 179.943 54.4955 180.498 53.8555C180.882 53.3862 181.181 53.1088 181.394 53.0235C181.65 52.8955 182.034 52.8315 182.546 52.8315H190.482C190.866 52.8315 191.186 52.9595 191.442 53.2155C191.741 53.4288 191.89 53.7275 191.89 54.1115C191.89 55.2208 191.25 56.5008 189.97 57.9515C188.733 59.4022 186.919 60.6608 184.53 61.7275C182.141 62.7942 179.325 63.3275 176.082 63.3275ZM181.458 42.6555V42.5275C181.458 40.4368 180.967 38.7942 179.986 37.5995C179.047 36.3622 177.725 35.7435 176.018 35.7435C174.354 35.7435 173.031 36.3622 172.05 37.5995C171.111 38.7942 170.642 40.4368 170.642 42.5275V42.6555H181.458ZM216.901 62.6875C208.069 62.6875 203.653 58.4848 203.653 50.0795V37.7915H198.661C198.192 37.7915 197.786 37.6422 197.445 37.3435C197.146 37.0448 196.997 36.6608 196.997 36.1915V31.0075C196.997 30.5382 197.146 30.1542 197.445 29.8555C197.786 29.5568 198.192 29.4075 198.661 29.4075H203.653V18.8475C203.653 18.3782 203.802 17.9942 204.101 17.6955C204.442 17.3968 204.826 17.2475 205.253 17.2475H212.677C213.146 17.2475 213.53 17.3968 213.829 17.6955C214.128 17.9942 214.277 18.3782 214.277 18.8475V29.4075H222.277C222.746 29.4075 223.13 29.5568 223.429 29.8555C223.77 30.1542 223.941 30.5382 223.941 31.0075V36.1915C223.941 36.6608 223.77 37.0448 223.429 37.3435C223.13 37.6422 222.746 37.7915 222.277 37.7915H214.277V49.1835C214.277 52.3408 215.493 53.9195 217.925 53.9195H222.853C223.322 53.9195 223.706 54.0688 224.005 54.3675C224.304 54.6662 224.453 55.0502 224.453 55.5195V61.0875C224.453 61.5142 224.304 61.8982 224.005 62.2395C223.706 62.5382 223.322 62.6875 222.853 62.6875H216.901ZM246.456 63.3275C241.421 63.3275 237.432 62.1542 234.488 59.8075C231.544 57.4608 230.072 54.2608 230.072 50.2075C230.072 47.6048 230.819 45.3222 232.312 43.3595C233.805 41.3968 235.939 39.6262 238.712 38.0475C237.091 36.2982 235.917 34.6768 235.192 33.1835C234.509 31.6475 234.168 30.0902 234.168 28.5115C234.168 26.4635 234.701 24.5862 235.768 22.8795C236.835 21.1728 238.413 19.8075 240.504 18.7835C242.595 17.7595 245.112 17.2475 248.056 17.2475C250.787 17.2475 253.155 17.7595 255.16 18.7835C257.208 19.7648 258.744 21.1088 259.768 22.8155C260.835 24.4795 261.368 26.3142 261.368 28.3195C261.368 30.7942 260.621 32.9275 259.128 34.7195C257.635 36.4688 255.416 38.1968 252.472 39.9035L260.088 47.5195C260.771 46.4102 261.347 45.3435 261.816 44.3195C262.328 43.2528 262.84 41.8662 263.352 40.1595C263.523 39.5622 263.971 39.2635 264.696 39.2635H271.16C271.501 39.2635 271.8 39.3915 272.056 39.6475C272.312 39.8608 272.44 40.1382 272.44 40.4795C272.397 41.9728 271.693 43.9995 270.328 46.5595C269.005 49.1195 267.533 51.3808 265.912 53.3435L273.08 60.5755C273.379 60.9595 273.528 61.2795 273.528 61.5355C273.528 61.8768 273.4 62.1542 273.144 62.3675C272.931 62.5808 272.653 62.6875 272.312 62.6875H263.608C262.925 62.6875 262.371 62.4742 261.944 62.0475L259.064 59.2955C255.779 61.9835 251.576 63.3275 246.456 63.3275ZM247.032 34.0155C248.739 33.1622 250.019 32.3302 250.872 31.5195C251.768 30.7088 252.216 29.7488 252.216 28.6395C252.216 27.4875 251.811 26.5702 251 25.8875C250.232 25.1622 249.251 24.7995 248.056 24.7995C246.947 24.7995 245.965 25.1622 245.112 25.8875C244.301 26.6128 243.896 27.5515 243.896 28.7035C243.896 29.4715 244.131 30.2822 244.6 31.1355C245.112 31.9462 245.923 32.9062 247.032 34.0155ZM246.456 55.1995C249.101 55.1995 251.299 54.4102 253.048 52.8315L244.536 44.3195C241.72 45.8128 240.312 47.6262 240.312 49.7595C240.312 51.3808 240.931 52.7035 242.168 53.7275C243.405 54.7088 244.835 55.1995 246.456 55.1995ZM282.116 62.6875C281.689 62.6875 281.305 62.5382 280.964 62.2395C280.665 61.8982 280.516 61.5142 280.516 61.0875V19.4875C280.516 19.0182 280.665 18.6342 280.964 18.3355C281.305 18.0368 281.689 17.8875 282.116 17.8875H289.028C290.052 17.8875 290.799 18.3568 291.268 19.2955L302.532 39.4555L313.796 19.2955C314.265 18.3568 315.012 17.8875 316.036 17.8875H322.884C323.353 17.8875 323.737 18.0368 324.036 18.3355C324.377 18.6342 324.548 19.0182 324.548 19.4875V61.0875C324.548 61.5568 324.377 61.9408 324.036 62.2395C323.737 62.5382 323.353 62.6875 322.884 62.6875H315.268C314.799 62.6875 314.393 62.5382 314.052 62.2395C313.753 61.9408 313.604 61.5568 313.604 61.0875V37.0875L306.436 50.4635C305.881 51.4448 305.135 51.9355 304.196 51.9355H300.868C300.313 51.9355 299.865 51.8075 299.524 51.5515C299.183 51.2955 298.884 50.9328 298.628 50.4635L291.396 37.0875V61.0875C291.396 61.5142 291.247 61.8982 290.948 62.2395C290.649 62.5382 290.265 62.6875 289.796 62.6875H282.116ZM349.836 63.3275C344.545 63.3275 340.471 62.0688 337.612 59.5515C334.753 57.0342 333.196 53.4928 332.94 48.9275C332.897 48.3728 332.876 47.4128 332.876 46.0475C332.876 44.6822 332.897 43.7222 332.94 43.1675C333.196 38.6448 334.796 35.1248 337.74 32.6075C340.684 30.0475 344.716 28.7675 349.836 28.7675C354.999 28.7675 359.052 30.0475 361.996 32.6075C364.94 35.1248 366.54 38.6448 366.796 43.1675C366.839 43.7222 366.86 44.6822 366.86 46.0475C366.86 47.4128 366.839 48.3728 366.796 48.9275C366.54 53.4928 364.983 57.0342 362.124 59.5515C359.265 62.0688 355.169 63.3275 349.836 63.3275ZM349.836 55.5195C351.713 55.5195 353.1 54.9648 353.996 53.8555C354.892 52.7035 355.404 50.9542 355.532 48.6075C355.575 48.1808 355.596 47.3275 355.596 46.0475C355.596 44.7675 355.575 43.9142 355.532 43.4875C355.404 41.1835 354.871 39.4555 353.932 38.3035C353.036 37.1515 351.671 36.5755 349.836 36.5755C346.295 36.5755 344.417 38.8795 344.204 43.4875L344.14 46.0475L344.204 48.6075C344.289 50.9542 344.78 52.7035 345.676 53.8555C346.615 54.9648 348.001 55.5195 349.836 55.5195ZM376.033 24.6075C375.564 24.6075 375.18 24.4582 374.881 24.1595C374.582 23.8608 374.433 23.4768 374.433 23.0075V17.2475C374.433 16.7782 374.582 16.3942 374.881 16.0955C375.18 15.7968 375.564 15.6475 376.033 15.6475H383.713C384.182 15.6475 384.566 15.7968 384.865 16.0955C385.164 16.3942 385.313 16.7782 385.313 17.2475V23.0075C385.313 23.4768 385.164 23.8608 384.865 24.1595C384.566 24.4582 384.182 24.6075 383.713 24.6075H376.033ZM376.097 62.6875C375.628 62.6875 375.244 62.5382 374.945 62.2395C374.646 61.9408 374.497 61.5568 374.497 61.0875V31.0075C374.497 30.5382 374.646 30.1542 374.945 29.8555C375.244 29.5568 375.628 29.4075 376.097 29.4075H383.649C384.118 29.4075 384.502 29.5568 384.801 29.8555C385.1 30.1542 385.249 30.5382 385.249 31.0075V61.0875C385.249 61.5142 385.1 61.8982 384.801 62.2395C384.502 62.5382 384.118 62.6875 383.649 62.6875H376.097Z" fill="#02897A"/>
            </svg>
            <a class="linkNav m-2 p-2" href="./accueil.php">ACCUEIL</a>
            <a class="linkNav m-2 p-2" href="./activites.php">ACTIVITES</a>
            <a class="linkNav m-2 p-2" href="./conseils.php">CONSEILS</a>
            <a class="linkNav m-2 p-2" href="./boutique.php">BOUTIQUE</a>
            <a class="linkNav m-2 p-2" href="./connexion.php">{$this->getSVGPers()}CONNEXION</a>
            <a class="linkNav m-2 p-2" href="./inscription.php">{$this->getSVGPers()}S'INSCRIRE</a>
        </nav>
        HTML;
    }

    private function getFooter() : string {
        return <<<HTML
            <footer class="footer" style="background-color: #242424; color: #E2E2E2">
              <div class="container">
                <div class="row justify-content-center">
                  <div class="col-md-3 col-sm-6">
                    <!--Column1-->
                    <div class="mt-5 mb-5">
                      <h4>Nous Contacter</h4>
                      <ul class="list-unstyled">
                        <li>03 25 56 35 96</li>
                        <li>contact@vetetmoi.fr</li>
                      </ul>
                    </div>
                  </div>
                    <div class="mt-5 mb-5">
                        <h4>Nos Réseaux</h4>
                        <a href="#">{$this->getSVGInsta()}</a>
                        <a href="#">{$this->getSVGYoutube()}</a>
                        <a href="#">{$this->getSVGTwitter()}</a></li>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="border-top: 1px solid #E2E2E2; padding: 10px;font-size: 12px;">
                        <p class="text-center">&copy; Copyright 2021 - Vet&Moi.  Tous droits réservés.</p>
                    </div>
                </div>
              </div>
            </footer>
        HTML;
    }
}
