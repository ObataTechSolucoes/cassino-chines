<?php

if (!function_exists('applyTheme')) {
    function applyTheme($theme)
    {
        $themes = [
        'pourple' => [            
            'Border_bottons_and_selected' => '#291F40',
            'bonus_color_dep' => '#291F40',
            'color_players' => '#291F40',
            'back_sub_color' => '#291F40',
            'botao_deposito_background_nav' => '#291F40',
            'botao_login_background_nav' => '#291F40',
            'botao_login_background_modal' => '#291F40',
            'botao_registro_background_modal' => '#291F40',
            'botao_registro_border_nav' => '#291F40',
            'botao_deposito_border_saq' => '#291F40',
            'botao_deposito_background_dep' => '#291F40',
            'botao_deposito_border_dep' => '#291F40',
            'botao_deposito_background_saq' => '#291F40',

            'search_back' => '#58418C',
            'placeholder_background' => '#58418C',
            'card_transaction' => '#58418C',
            'background_color_category' => '#58418C',
            'background_bottom_navigation' => '#58418C',
            'background_bottom_navigation_dark' => '#58418C',
            'footer_color' => '#58418C',
            'footer_color_dark' => '#58418C',
            'side_menu' => '#58418C',
            'side_menu_dark' => '#58418C',
            'navtop_color' => '#58418C',
            'navtop_color_dark' => '#58418C',

            'item_sub_color' => '#6B53A2',
            'background_color_jackpot' => '#6B53A2',
            'value_color_jackpot' => '#6B53A2',
            'sidebar_color' => '#6B53A2',
            'sidebar_color_dark' => '#6B53A2',
            'background_base' => '#6B53A2',
            'background_base_dark' => '#6B53A2',
            'background_color_cassino' => '#6B53A2',

            'search_border_color' => '#8870B8',
            'borders_and_dividers_colors' => '#8870B8'                       
        ],

        'black' => [

            'Border_bottons_and_selected' => '#2FA33F',
            'bonus_color_dep' => '#2FA33F',
            'color_players' => '#2FA33F',
            'back_sub_color' => '#2FA33F',
            'botao_deposito_background_nav' => '#2FA33F',
            'botao_login_background_nav' => '#2FA33F',
            'botao_login_background_modal' => '#2FA33F',
            'botao_registro_background_modal' => '#2FA33F',
            'botao_registro_border_nav' => '#2FA33F',
            'botao_deposito_border_saq' => '#2FA33F',
            'botao_deposito_background_dep' => '#2FA33F',
            'botao_deposito_border_dep' => '#2FA33F',
            'botao_deposito_background_saq' => '#2FA33F',

            'search_back' => '#161D29',
            'placeholder_background' => '#161D29',
            'card_transaction' => '#161D29',
            'background_color_category' => '#161D29',
            'background_bottom_navigation' => '#161D29',
            'background_bottom_navigation_dark' => '#161D29',
            'footer_color' => '#161D29',
            'footer_color_dark' => '#161D29',
            'side_menu' => '#161D29',
            'side_menu_dark' => '#161D29',
            'navtop_color' => '#161D29',
            'navtop_color_dark' => '#161D29',

            'item_sub_color' => '#0D131C',
            'background_color_jackpot' => '#0D131C',
            'value_color_jackpot' => '#0D131C',
            'sidebar_color' => '#0D131C',
            'sidebar_color_dark' => '#0D131C',
            'background_base' => '#0D131C',
            'background_base_dark' => '#0D131C',
            'background_color_cassino' => '#0D131C',

            'search_border_color' => '#6D7A91',
            'borders_and_dividers_colors' => '#6D7A91'  

        ],
        'blue' => [

            'Border_bottons_and_selected' => '#2FA33F',
            'bonus_color_dep' => '#2FA33F',
            'color_players' => '#2FA33F',
            'back_sub_color' => '#2FA33F',
            'botao_deposito_background_nav' => '#2FA33F',
            'botao_login_background_nav' => '#2FA33F',
            'botao_login_background_modal' => '#2FA33F',
            'botao_registro_background_modal' => '#2FA33F',
            'botao_registro_border_nav' => '#2FA33F',
            'botao_deposito_border_saq' => '#2FA33F',
            'botao_deposito_background_dep' => '#2FA33F',
            'botao_deposito_border_dep' => '#2FA33F',
            'botao_deposito_background_saq' => '#2FA33F',

            'search_back' => '#092C71',
            'placeholder_background' => '#092C71',
            'card_transaction' => '#092C71',
            'background_color_category' => '#092C71',
            'background_bottom_navigation' => '#092C71',
            'background_bottom_navigation_dark' => '#092C71',
            'footer_color' => '#092C71',
            'footer_color_dark' => '#092C71',
            'side_menu' => '#092C71',
            'side_menu_dark' => '#092C71',
            'navtop_color' => '#092C71',
            'navtop_color_dark' => '#092C71',

            'item_sub_color' => '#0C3588',
            'background_color_jackpot' => '#0C3588',
            'value_color_jackpot' => '#0C3588',
            'sidebar_color' => '#0C3588',
            'sidebar_color_dark' => '#0C3588',
            'background_base' => '#0C3588',
            'background_base_dark' => '#0C3588',
            'background_color_cassino' => '#0C3588',

            'search_border_color' => '#568CF7',
            'borders_and_dividers_colors' => '#568CF7'
            
        ],
        'red' => [

            'Border_bottons_and_selected' => '#FE3856',
            'bonus_color_dep' => '#FE3856',
            'color_players' => '#FE3856',
            'back_sub_color' => '#FE3856',
            'botao_deposito_background_nav' => '#FE3856',
            'botao_login_background_nav' => '#FE3856',
            'botao_login_background_modal' => '#FE3856',
            'botao_registro_background_modal' => '#FE3856',
            'botao_registro_border_nav' => '#FE3856',
            'botao_deposito_border_saq' => '#FE3856',
            'botao_deposito_background_dep' => '#FE3856',
            'botao_deposito_border_dep' => '#FE3856',
            'botao_deposito_background_saq' => '#FE3856',

            'search_back' => '#630B20',
            'placeholder_background' => '#630B20',
            'card_transaction' => '#630B20',
            'background_color_category' => '#630B20',
            'background_bottom_navigation' => '#630B20',
            'background_bottom_navigation_dark' => '#630B20',
            'footer_color' => '#630B20',
            'footer_color_dark' => '#630B20',
            'side_menu' => '#630B20',
            'side_menu_dark' => '#630B20',
            'navtop_color' => '#630B20',
            'navtop_color_dark' => '#630B20',

            'item_sub_color' => '#781830',
            'background_color_jackpot' => '#781830',
            'value_color_jackpot' => '#781830',
            'sidebar_color' => '#781830',
            'sidebar_color_dark' => '#781830',
            'background_base' => '#781830',
            'background_base_dark' => '#781830',
            'background_color_cassino' => '#781830',

            'search_border_color' => '#E08EA3',
            'borders_and_dividers_colors' => '#E08EA3'
                    
        ],
        'pink' => [

            'Border_bottons_and_selected' => '#99002B',
            'bonus_color_dep' => '#99002B',
            'color_players' => '#99002B',
            'back_sub_color' => '#99002B',
            'botao_deposito_background_nav' => '#99002B',
            'botao_login_background_nav' => '#99002B',
            'botao_login_background_modal' => '#99002B',
            'botao_registro_background_modal' => '#99002B',
            'botao_registro_border_nav' => '#99002B',
            'botao_deposito_border_saq' => '#99002B',
            'botao_deposito_background_dep' => '#99002B',
            'botao_deposito_border_dep' => '#99002B',
            'botao_deposito_background_saq' => '#99002B',

            'search_back' => '#C3607C',
            'placeholder_background' => '#C3607C',
            'card_transaction' => '#C3607C',
            'background_color_category' => '#C3607C',
            'background_bottom_navigation' => '#C3607C',
            'background_bottom_navigation_dark' => '#C3607C',
            'footer_color' => '#C3607C',
            'footer_color_dark' => '#C3607C',
            'side_menu' => '#C3607C',
            'side_menu_dark' => '#C3607C',
            'navtop_color' => '#C3607C',
            'navtop_color_dark' => '#C3607C',

            'item_sub_color' => '#D57490',
            'background_color_jackpot' => '#D57490',
            'value_color_jackpot' => '#D57490',
            'sidebar_color' => '#D57490',
            'sidebar_color_dark' => '#D57490',
            'background_base' => '#D57490',
            'background_base_dark' => '#D57490',
            'background_color_cassino' => '#D57490',

            'search_border_color' => '#E7BFCB',
            'borders_and_dividers_colors' => '#E7BFCB'
                     
        ],
        'limon' => [

            'Border_bottons_and_selected' => '#354505',
            'bonus_color_dep' => '#354505',
            'color_players' => '#354505',
            'back_sub_color' => '#354505',
            'botao_deposito_background_nav' => '#354505',
            'botao_login_background_nav' => '#354505',
            'botao_login_background_modal' => '#354505',
            'botao_registro_background_modal' => '#354505',
            'botao_registro_border_nav' => '#354505',
            'botao_deposito_border_saq' => '#354505',
            'botao_deposito_background_dep' => '#354505',
            'botao_deposito_border_dep' => '#354505',
            'botao_deposito_background_saq' => '#354505',

            'search_back' => '#5C780B',
            'placeholder_background' => '#5C780B',
            'card_transaction' => '#5C780B',
            'background_color_category' => '#5C780B',
            'background_bottom_navigation' => '#5C780B',
            'background_bottom_navigation_dark' => '#5C780B',
            'footer_color' => '#5C780B',
            'footer_color_dark' => '#5C780B',
            'side_menu' => '#5C780B',
            'side_menu_dark' => '#5C780B',
            'navtop_color' => '#5C780B',
            'navtop_color_dark' => '#5C780B',

            'item_sub_color' => '#8BA638',
            'background_color_jackpot' => '#8BA638',
            'value_color_jackpot' => '#8BA638',
            'sidebar_color' => '#8BA638',
            'sidebar_color_dark' => '#8BA638',
            'background_base' => '#8BA638',
            'background_base_dark' => '#8BA638',
            'background_color_cassino' => '#8BA638',

            'search_border_color' => '#F2FBB7',
            'borders_and_dividers_colors' => '#F2FBB7'
            
        ],
        'brown' => [

            'Border_bottons_and_selected' => '#FE3856',
            'bonus_color_dep' => '#FE3856',
            'color_players' => '#FE3856',
            'back_sub_color' => '#FE3856',
            'botao_deposito_background_nav' => '#FE3856',
            'botao_login_background_nav' => '#FE3856',
            'botao_login_background_modal' => '#FE3856',
            'botao_registro_background_modal' => '#FE3856',
            'botao_registro_border_nav' => '#FE3856',
            'botao_deposito_border_saq' => '#FE3856',
            'botao_deposito_background_dep' => '#FE3856',
            'botao_deposito_border_dep' => '#FE3856',
            'botao_deposito_background_saq' => '#FE3856',

            'search_back' => '#544038',
            'placeholder_background' => '#544038',
            'card_transaction' => '#544038',
            'background_color_category' => '#544038',
            'background_bottom_navigation' => '#544038',
            'background_bottom_navigation_dark' => '#544038',
            'footer_color' => '#544038',
            'footer_color_dark' => '#544038',
            'side_menu' => '#544038',
            'side_menu_dark' => '#544038',
            'navtop_color' => '#544038',
            'navtop_color_dark' => '#544038',

            'item_sub_color' => '#634C42',
            'background_color_jackpot' => '#634C42',
            'value_color_jackpot' => '#634C42',
            'sidebar_color' => '#634C42',
            'sidebar_color_dark' => '#634C42',
            'background_base' => '#634C42',
            'background_base_dark' => '#634C42',
            'background_color_cassino' => '#634C42',

            'search_border_color' => '#C1B7B3',
            'borders_and_dividers_colors' => '#C1B7B3'
                     
        ],
        'skyblue' => [

            'Border_bottons_and_selected' => '#EAB700',
            'bonus_color_dep' => '#EAB700',
            'color_players' => '#EAB700',
            'back_sub_color' => '#EAB700',
            'botao_deposito_background_nav' => '#EAB700',
            'botao_login_background_nav' => '#EAB700',
            'botao_login_background_modal' => '#EAB700',
            'botao_registro_background_modal' => '#EAB700',
            'botao_registro_border_nav' => '#EAB700',
            'botao_deposito_border_saq' => '#EAB700',
            'botao_deposito_background_dep' => '#EAB700',
            'botao_deposito_border_dep' => '#EAB700',
            'botao_deposito_background_saq' => '#EAB700',

            'search_back' => '#015ED4',
            'placeholder_background' => '#015ED4',
            'card_transaction' => '#015ED4',
            'background_color_category' => '#015ED4',
            'background_bottom_navigation' => '#015ED4',
            'background_bottom_navigation_dark' => '#015ED4',
            'footer_color' => '#015ED4',
            'footer_color_dark' => '#015ED4',
            'side_menu' => '#015ED4',
            'side_menu_dark' => '#015ED4',
            'navtop_color' => '#015ED4',
            'navtop_color_dark' => '#015ED4',

            'item_sub_color' => '#388AF0',
            'background_color_jackpot' => '#388AF0',
            'value_color_jackpot' => '#388AF0',
            'sidebar_color' => '#388AF0',
            'sidebar_color_dark' => '#388AF0',
            'background_base' => '#388AF0',
            'background_base_dark' => '#388AF0',
            'background_color_cassino' => '#388AF0',

            'search_border_color' => '#6DAFEE',
            'borders_and_dividers_colors' => '#6DAFEE'
                    
        ],
        'whiteblue' => [

            'Border_bottons_and_selected' => '#1F2B4D',
            'bonus_color_dep' => '#1F2B4D',
            'color_players' => '#1F2B4D',
            'back_sub_color' => '#1F2B4D',
            'botao_deposito_background_nav' => '#1F2B4D',
            'botao_login_background_nav' => '#1F2B4D',
            'botao_login_background_modal' => '#1F2B4D',
            'botao_registro_background_modal' => '#1F2B4D',
            'botao_registro_border_nav' => '#1F2B4D',
            'botao_deposito_border_saq' => '#1F2B4D',
            'botao_deposito_background_dep' => '#1F2B4D',
            'botao_deposito_border_dep' => '#1F2B4D',
            'botao_deposito_background_saq' => '#1F2B4D',

            'search_back' => '#5375D1',
            'placeholder_background' => '#5375D1',
            'card_transaction' => '#5375D1',
            'background_color_category' => '#5375D1',
            'background_bottom_navigation' => '#5375D1',
            'background_bottom_navigation_dark' => '#5375D1',
            'footer_color' => '#5375D1',
            'footer_color_dark' => '#5375D1',
            'side_menu' => '#5375D1',
            'side_menu_dark' => '#5375D1',
            'navtop_color' => '#5375D1',
            'navtop_color_dark' => '#5375D1',

            'item_sub_color' => '#8EABFA',
            'background_color_jackpot' => '#8EABFA',
            'value_color_jackpot' => '#8EABFA',
            'sidebar_color' => '#8EABFA',
            'sidebar_color_dark' => '#8EABFA',
            'background_base' => '#8EABFA',
            'background_base_dark' => '#8EABFA',
            'background_color_cassino' => '#8EABFA',

            'search_border_color' => '#BAC8ED',
            'borders_and_dividers_colors' => '#BAC8ED'
            
        ],
        'greendark' => [

            'Border_bottons_and_selected' => '#2FA33F',
            'bonus_color_dep' => '#2FA33F',
            'color_players' => '#2FA33F',
            'back_sub_color' => '#2FA33F',
            'botao_deposito_background_nav' => '#2FA33F',
            'botao_login_background_nav' => '#2FA33F',
            'botao_login_background_modal' => '#2FA33F',
            'botao_registro_background_modal' => '#2FA33F',
            'botao_registro_border_nav' => '#2FA33F',
            'botao_deposito_border_saq' => '#2FA33F',
            'botao_deposito_background_dep' => '#2FA33F',
            'botao_deposito_border_dep' => '#2FA33F',
            'botao_deposito_background_saq' => '#2FA33F',

            'search_back' => '#00352F',
            'placeholder_background' => '#00352F',
            'card_transaction' => '#00352F',
            'background_color_category' => '#00352F',
            'background_bottom_navigation' => '#00352F',
            'background_bottom_navigation_dark' => '#00352F',
            'footer_color' => '#00352F',
            'footer_color_dark' => '#00352F',
            'side_menu' => '#00352F',
            'side_menu_dark' => '#00352F',
            'navtop_color' => '#00352F',
            'navtop_color_dark' => '#00352F',

            'item_sub_color' => '#014D45',
            'background_color_jackpot' => '#014D45',
            'value_color_jackpot' => '#014D45',
            'sidebar_color' => '#014D45',
            'sidebar_color_dark' => '#014D45',
            'background_base' => '#014D45',
            'background_base_dark' => '#014D45',
            'background_color_cassino' => '#014D45',

            'search_border_color' => '#4FCABC',
            'borders_and_dividers_colors' => '#4FCABC'
                    
        ],
        'orange' => [

            'Border_bottons_and_selected' => '#551F0E',
            'bonus_color_dep' => '#551F0E',
            'color_players' => '#551F0E',
            'back_sub_color' => '#551F0E',
            'botao_deposito_background_nav' => '#551F0E',
            'botao_login_background_nav' => '#551F0E',
            'botao_login_background_modal' => '#551F0E',
            'botao_registro_background_modal' => '#551F0E',
            'botao_registro_border_nav' => '#551F0EF',
            'botao_deposito_border_saq' => '#551F0E',
            'botao_deposito_background_dep' => '#551F0E',
            'botao_deposito_border_dep' => '#551F0E',
            'botao_deposito_background_saq' => '#551F0E',

            'search_back' => '#F87607',
            'placeholder_background' => '#F87607',
            'card_transaction' => '#F87607',
            'background_color_category' => '#F87607',
            'background_bottom_navigation' => '#F87607',
            'background_bottom_navigation_dark' => '#F87607',
            'footer_color' => '#F87607',
            'footer_color_dark' => '#F87607',
            'side_menu' => '#F87607',
            'side_menu_dark' => '#F87607',
            'navtop_color' => '#F87607',
            'navtop_color_dark' => '#F87607',

            'item_sub_color' => '#F48E39',
            'background_color_jackpot' => '#F48E39',
            'value_color_jackpot' => '#F48E39',
            'sidebar_color' => '#F48E39',
            'sidebar_color_dark' => '#F48E39',
            'background_base' => '#F48E39',
            'background_base_dark' => '#F48E39',
            'background_color_cassino' => '#F48E39',

            'search_border_color' => '#FEE7CA',
            'borders_and_dividers_colors' => '#FEE7CA'
                   
        ],
        'green' => [

            'Border_bottons_and_selected' => '#EAB700',
            'bonus_color_dep' => '#EAB700',
            'color_players' => '#EAB700',
            'back_sub_color' => '#EAB700',
            'botao_deposito_background_nav' => '#EAB700',
            'botao_login_background_nav' => '#EAB700',
            'botao_login_background_modal' => '#EAB700',
            'botao_registro_background_modal' => '#EAB700',
            'botao_registro_border_nav' => '#EAB700',
            'botao_deposito_border_saq' => '#EAB700',
            'botao_deposito_background_dep' => '#EAB700',
            'botao_deposito_border_dep' => '#EAB700',
            'botao_deposito_background_saq' => '#EAB700',

            'search_back' => '#0D5E2B',
            'placeholder_background' => '#0D5E2B',
            'card_transaction' => '#0D5E2B',
            'background_color_category' => '#0D5E2B',
            'background_bottom_navigation' => '#0D5E2B',
            'background_bottom_navigation_dark' => '#0D5E2B',
            'footer_color' => '#0D5E2B',
            'footer_color_dark' => '#0D5E2B',
            'side_menu' => '#0D5E2B',
            'side_menu_dark' => '#0D5E2B',
            'navtop_color' => '#0D5E2B',
            'navtop_color_dark' => '#0D5E2B',

            'item_sub_color' => '#0C913F',
            'background_color_jackpot' => '#0C913F',
            'value_color_jackpot' => '#0C913F',
            'sidebar_color' => '#0C913F',
            'sidebar_color_dark' => '#0C913F',
            'background_base' => '#0C913F',
            'background_base_dark' => '#0C913F',
            'background_color_cassino' => '#0C913F',

            'search_border_color' => '#7BD39B',
            'borders_and_dividers_colors' => '#7BD39B'
            
        ],
        'yellow' => [

            'Border_bottons_and_selected' => '#373638',
            'bonus_color_dep' => '#373638',
            'color_players' => '#F3F2F5',
            'back_sub_color' => '#373638',
            'botao_deposito_background_nav' => '#373638',
            'botao_login_background_nav' => '#373638',
            'botao_login_background_modal' => '#373638',
            'botao_registro_background_modal' => '#373638',
            'botao_registro_border_nav' => '#373638',
            'botao_deposito_border_saq' => '#373638',
            'botao_deposito_background_dep' => '#373638',
            'botao_deposito_border_dep' => '#373638',
            'botao_deposito_background_saq' => '#373638',

            'search_back' => '#F2A200',
            'placeholder_background' => '#F2A200',
            'card_transaction' => '#F2A200',
            'background_color_category' => '#F2A200',
            'background_bottom_navigation' => '#F2A200',
            'background_bottom_navigation_dark' => '#F2A200',
            'footer_color' => '#F2A200',
            'footer_color_dark' => '#F2A200',
            'side_menu' => '#F2A200',
            'side_menu_dark' => '#F2A200',
            'navtop_color' => '#F2A200',
            'navtop_color_dark' => '#F2A200',

            'item_sub_color' => '#FDD155',
            'background_color_jackpot' => '#FDD155',
            'value_color_jackpot' => '#FDD155',
            'sidebar_color' => '#FDD155',
            'sidebar_color_dark' => '#FDD155',
            'background_base' => '#FDD155',
            'background_base_dark' => '#FDD155',
            'background_color_cassino' => '#FDD155',

            'search_border_color' => '#FEE8A8',
            'borders_and_dividers_colors' => '#FEE8A8'
            
        ],
        'greenwhite' => [

            'Border_bottons_and_selected' => '#0F4D3B',
            'bonus_color_dep' => '#0F4D3B',
            'color_players' => '#0F4D3B',
            'back_sub_color' => '#0F4D3B',
            'botao_deposito_background_nav' => '#0F4D3B',
            'botao_login_background_nav' => '#0F4D3B',
            'botao_login_background_modal' => '#0F4D3B',
            'botao_registro_background_modal' => '#0F4D3B',
            'botao_registro_border_nav' => '#0F4D3B',
            'botao_deposito_border_saq' => '#0F4D3B',
            'botao_deposito_background_dep' => '#0F4D3B',
            'botao_deposito_border_dep' => '#0F4D3B',
            'botao_deposito_background_saq' => '#0F4D3B',

            'search_back' => '#21A57D',
            'placeholder_background' => '#21A57D',
            'card_transaction' => '#21A57D',
            'background_color_category' => '#21A57D',
            'background_bottom_navigation' => '#21A57D',
            'background_bottom_navigation_dark' => '#21A57D',
            'footer_color' => '#21A57D',
            'footer_color_dark' => '#21A57D',
            'side_menu' => '#21A57D',
            'side_menu_dark' => '#21A57D',
            'navtop_color' => '#21A57D',
            'navtop_color_dark' => '#21A57D',

            'item_sub_color' => '#4EBB95',
            'background_color_jackpot' => '#4EBB95',
            'value_color_jackpot' => '#4EBB95',
            'sidebar_color' => '#4EBB95',
            'sidebar_color_dark' => '#4EBB95',
            'background_base' => '#4EBB95',
            'background_base_dark' => '#4EBB95',
            'background_color_cassino' => '#4EBB95',

            'search_border_color' => '#89F1CD',
            'borders_and_dividers_colors' => '#89F1CD'
            
        ],
        'gray' => [

            'Border_bottons_and_selected' => '#373638',
            'bonus_color_dep' => '#373638',
            'color_players' => '#F3F2F5',
            'back_sub_color' => '#373638',
            'botao_deposito_background_nav' => '#373638',
            'botao_login_background_nav' => '#373638',
            'botao_login_background_modal' => '#373638',
            'botao_registro_background_modal' => '#373638',
            'botao_registro_border_nav' => '#373638',
            'botao_deposito_border_saq' => '#373638',
            'botao_deposito_background_dep' => '#373638',
            'botao_deposito_border_dep' => '#373638',
            'botao_deposito_background_saq' => '#373638',

            'search_back' => '#648872',
            'placeholder_background' => '#648872',
            'card_transaction' => '#648872',
            'background_color_category' => '#648872',
            'background_bottom_navigation' => '#648872',
            'background_bottom_navigation_dark' => '#648872',
            'footer_color' => '#648872',
            'footer_color_dark' => '#648872',
            'side_menu' => '#648872',
            'side_menu_dark' => '#648872',
            'navtop_color' => '#648872',
            'navtop_color_dark' => '#648872',

            'item_sub_color' => '#799B86',
            'background_color_jackpot' => '#799B86',
            'value_color_jackpot' => '#799B86',
            'sidebar_color' => '#799B86',
            'sidebar_color_dark' => '#799B86',
            'background_base' => '#799B86',
            'background_base_dark' => '#799B86',
            'background_color_cassino' => '#799B86',

            'search_border_color' => '#A0D3B3',
            'borders_and_dividers_colors' => '#A0D3B3'
            
        ],
        'rose' => [
    'Border_bottons_and_selected' => '#5A2D2F',  // Tom mais escuro de vermelho
    'bonus_color_dep' => '#5A2D2F',
    'color_players' => '#5A2D2F',
    'back_sub_color' => '#5A2D2F',
    'botao_deposito_background_nav' => '#5A2D2F',
    'botao_login_background_nav' => '#5A2D2F',
    'botao_login_background_modal' => '#5A2D2F',
    'botao_registro_background_modal' => '#5A2D2F',
    'botao_registro_border_nav' => '#5A2D2F',
    'botao_deposito_border_saq' => '#5A2D2F',
    'botao_deposito_background_dep' => '#5A2D2F',
    'botao_deposito_border_dep' => '#5A2D2F',
    'botao_deposito_background_saq' => '#5A2D2F',

    'search_back' => '#9A4F4A',  // Tom médio de vermelho
    'placeholder_background' => '#9A4F4A',
    'card_transaction' => '#9A4F4A',
    'background_color_category' => '#9A4F4A',
    'background_bottom_navigation' => '#9A4F4A',
    'background_bottom_navigation_dark' => '#9A4F4A',
    'footer_color' => '#9A4F4A',
    'footer_color_dark' => '#9A4F4A',
    'side_menu' => '#9A4F4A',
    'side_menu_dark' => '#9A4F4A',
    'navtop_color' => '#9A4F4A',
    'navtop_color_dark' => '#9A4F4A',

    'item_sub_color' => '#C97A72',  // Tom mais claro de vermelho
    'background_color_jackpot' => '#C97A72',
    'value_color_jackpot' => '#C97A72',
    'sidebar_color' => '#C97A72',
    'sidebar_color_dark' => '#C97A72',
    'background_base' => '#C97A72',
    'background_base_dark' => '#C97A72',
    'background_color_cassino' => '#C97A72',

    'search_border_color' => '#F1B3AE',  // Tom muito suave de vermelho
    'borders_and_dividers_colors' => '#F1B3AE'
],
'amarelo' => [
    'Border_bottons_and_selected' => '#D6002A',  // Tom mais escuro de vermelho
    'bonus_color_dep' => '#D6002A',
    'color_players' => '#D6002A',
    'back_sub_color' => '#D6002A',
    'botao_deposito_background_nav' => '#D6002A',
    'botao_login_background_nav' => '#D6002A',
    'botao_login_background_modal' => '#D6002A',
    'botao_registro_background_modal' => '#D6002A',
    'botao_registro_border_nav' => '#D6002A',
    'botao_deposito_border_saq' => '#D6002A',
    'botao_deposito_background_dep' => '#D6002A',
    'botao_deposito_border_dep' => '#D6002A',
    'botao_deposito_background_saq' => '#D6002A',

    'search_back' => '#FC0134',  // Tom principal de vermelho
    'placeholder_background' => '#FC0134',
    'card_transaction' => '#FC0134',
    'background_color_category' => '#FC0134',
    'background_bottom_navigation' => '#FC0134',
    'background_bottom_navigation_dark' => '#FC0134',
    'footer_color' => '#FC0134',
    'footer_color_dark' => '#FC0134',
    'side_menu' => '#FC0134',
    'side_menu_dark' => '#FC0134',
    'navtop_color' => '#FC0134',
    'navtop_color_dark' => '#FC0134',

    'item_sub_color' => '#F94F5D',  // Tom mais claro de vermelho
    'background_color_jackpot' => '#F94F5D',
    'value_color_jackpot' => '#F94F5D',
    'sidebar_color' => '#F94F5D',
    'sidebar_color_dark' => '#F94F5D',
    'background_base' => '#F94F5D',
    'background_base_dark' => '#F94F5D',
    'background_color_cassino' => '#F94F5D',

    'search_border_color' => '#F7A2B0',  // Tom suave de vermelho
    'borders_and_dividers_colors' => '#F7A2B0'
],
'redvivo' => [
    'Border_bottons_and_selected' => '#A3002A',  // Tom mais escuro de vermelho
    'bonus_color_dep' => '#A3002A',
    'color_players' => '#A3002A',
    'back_sub_color' => '#A3002A',
    'botao_deposito_background_nav' => '#A3002A',
    'botao_login_background_nav' => '#A3002A',
    'botao_login_background_modal' => '#A3002A',
    'botao_registro_background_modal' => '#A3002A',
    'botao_registro_border_nav' => '#A3002A',
    'botao_deposito_border_saq' => '#A3002A',
    'botao_deposito_background_dep' => '#A3002A',
    'botao_deposito_border_dep' => '#A3002A',
    'botao_deposito_background_saq' => '#A3002A',

    'search_back' => '#D00C33',  // Tom principal de vermelho
    'placeholder_background' => '#D00C33',
    'card_transaction' => '#D00C33',
    'background_color_category' => '#D00C33',
    'background_bottom_navigation' => '#D00C33',
    'background_bottom_navigation_dark' => '#D00C33',
    'footer_color' => '#D00C33',
    'footer_color_dark' => '#D00C33',
    'side_menu' => '#D00C33',
    'side_menu_dark' => '#D00C33',
    'navtop_color' => '#D00C33',
    'navtop_color_dark' => '#D00C33',

    'item_sub_color' => '#E14D5A',  // Tom mais claro de vermelho
    'background_color_jackpot' => '#E14D5A',
    'value_color_jackpot' => '#E14D5A',
    'sidebar_color' => '#E14D5A',
    'sidebar_color_dark' => '#E14D5A',
    'background_base' => '#E14D5A',
    'background_base_dark' => '#E14D5A',
    'background_color_cassino' => '#E14D5A',

    'search_border_color' => '#F29C9F',  // Tom suave de vermelho
    'borders_and_dividers_colors' => '#F29C9F'
],


'brown2' => [
    'Border_bottons_and_selected' => '#2E1A08',  // Tom mais escuro de marrom
    'bonus_color_dep' => '#2E1A08',
    'color_players' => '#2E1A08',
    'back_sub_color' => '#2E1A08',
    'botao_deposito_background_nav' => '#2E1A08',
    'botao_login_background_nav' => '#2E1A08',
    'botao_login_background_modal' => '#2E1A08',
    'botao_registro_background_modal' => '#2E1A08',
    'botao_registro_border_nav' => '#2E1A08',
    'botao_deposito_border_saq' => '#2E1A08',
    'botao_deposito_background_dep' => '#2E1A08',
    'botao_deposito_border_dep' => '#2E1A08',
    'botao_deposito_background_saq' => '#2E1A08',

    'search_back' => '#7A4F34',  // Tom médio de marrom
    'placeholder_background' => '#7A4F34',
    'card_transaction' => '#7A4F34',
    'background_color_category' => '#7A4F34',
    'background_bottom_navigation' => '#7A4F34',
    'background_bottom_navigation_dark' => '#7A4F34',
    'footer_color' => '#7A4F34',
    'footer_color_dark' => '#7A4F34',
    'side_menu' => '#7A4F34',
    'side_menu_dark' => '#7A4F34',
    'navtop_color' => '#7A4F34',
    'navtop_color_dark' => '#7A4F34',

    'item_sub_color' => '#A7744C',  // Tom mais claro de marrom
    'background_color_jackpot' => '#A7744C',
    'value_color_jackpot' => '#A7744C',
    'sidebar_color' => '#A7744C',
    'sidebar_color_dark' => '#A7744C',
    'background_base' => '#A7744C',
    'background_base_dark' => '#A7744C',
    'background_color_cassino' => '#A7744C',

    'search_border_color' => '#D2A57E',  // Tom suave de marrom
    'borders_and_dividers_colors' => '#D2A57E'
],
'blue_purple' => [
    'Border_bottons_and_selected' => '#2D2F2B',  // Tom mais escuro de verde-acinzentado
    'bonus_color_dep' => '#2D2F2B',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#2D2F2B',
    'botao_deposito_background_nav' => '#2D2F2B',
    'botao_login_background_nav' => '#2D2F2B',
    'botao_login_background_modal' => '#2D2F2B',
    'botao_registro_background_modal' => '#2D2F2B',
    'botao_registro_border_nav' => '#2D2F2B',
    'botao_deposito_border_saq' => '#2D2F2B',
    'botao_deposito_background_dep' => '#2D2F2B',
    'botao_deposito_border_dep' => '#2D2F2B',
    'botao_deposito_background_saq' => '#2D2F2B',

    'search_back' => '#5C6358',  // Tom médio de verde-acinzentado
    'placeholder_background' => '#5C6358',
    'card_transaction' => '#5C6358',
    'background_color_category' => '#5C6358',
    'background_bottom_navigation' => '#5C6358',
    'background_bottom_navigation_dark' => '#5C6358',
    'footer_color' => '#5C6358',
    'footer_color_dark' => '#5C6358',
    'side_menu' => '#5C6358',
    'side_menu_dark' => '#5C6358',
    'navtop_color' => '#5C6358',
    'navtop_color_dark' => '#5C6358',

    'item_sub_color' => '#748177',  // Tom mais claro de verde-acinzentado
    'background_color_jackpot' => '#748177',
    'value_color_jackpot' => '#748177',
    'sidebar_color' => '#748177',
    'sidebar_color_dark' => '#748177',
    'background_base' => '#748177',
    'background_base_dark' => '#748177',
    'background_color_cassino' => '#748177',

    'search_border_color' => '#A2A79A',  // Tom suave de verde-acinzentado
    'borders_and_dividers_colors' => '#A2A79A'
],
'Gold' => [
    'Border_bottons_and_selected' => '#9E6806',  // Tom mais escuro de amarelo-ouro
    'bonus_color_dep' => '#9E6806',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#9E6806',
    'botao_deposito_background_nav' => '#9E6806',
    'botao_login_background_nav' => '#9E6806',
    'botao_login_background_modal' => '#9E6806',
    'botao_registro_background_modal' => '#9E6806',
    'botao_registro_border_nav' => '#9E6806',
    'botao_deposito_border_saq' => '#9E6806',
    'botao_deposito_background_dep' => '#9E6806',
    'botao_deposito_border_dep' => '#9E6806',
    'botao_deposito_background_saq' => '#9E6806',

    'search_back' => '#BC7D08',  // Tom principal de amarelo-ouro
    'placeholder_background' => '#BC7D08',
    'card_transaction' => '#BC7D08',
    'background_color_category' => '#BC7D08',
    'background_bottom_navigation' => '#BC7D08',
    'background_bottom_navigation_dark' => '#BC7D08',
    'footer_color' => '#BC7D08',
    'footer_color_dark' => '#BC7D08',
    'side_menu' => '#BC7D08',
    'side_menu_dark' => '#BC7D08',
    'navtop_color' => '#BC7D08',
    'navtop_color_dark' => '#BC7D08',

    'item_sub_color' => '#D38A3C',  // Tom mais claro de amarelo-ouro
    'background_color_jackpot' => '#D38A3C',
    'value_color_jackpot' => '#D38A3C',
    'sidebar_color' => '#D38A3C',
    'sidebar_color_dark' => '#D38A3C',
    'background_base' => '#D38A3C',
    'background_base_dark' => '#D38A3C',
    'background_color_cassino' => '#D38A3C',

    'search_border_color' => '#F1B56E',  // Tom suave de amarelo-ouro
    'borders_and_dividers_colors' => '#F1B56E'
],
'bluesofic' => [
    'Border_bottons_and_selected' => '#00224D',  // Tom mais escuro de azul
    'bonus_color_dep' => '#00224D',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#00224D',
    'botao_deposito_background_nav' => '#00224D',
    'botao_login_background_nav' => '#00224D',
    'botao_login_background_modal' => '#00224D',
    'botao_registro_background_modal' => '#00224D',
    'botao_registro_border_nav' => '#00224D',
    'botao_deposito_border_saq' => '#00224D',
    'botao_deposito_background_dep' => '#00224D',
    'botao_deposito_border_dep' => '#00224D',
    'botao_deposito_background_saq' => '#00224D',

    'search_back' => '#003366',  // Tom principal de azul
    'placeholder_background' => '#003366',
    'card_transaction' => '#003366',
    'background_color_category' => '#003366',
    'background_bottom_navigation' => '#003366',
    'background_bottom_navigation_dark' => '#003366',
    'footer_color' => '#003366',
    'footer_color_dark' => '#003366',
    'side_menu' => '#003366',
    'side_menu_dark' => '#003366',
    'navtop_color' => '#003366',
    'navtop_color_dark' => '#003366',

    'item_sub_color' => '#336699',  // Tom mais claro de azul
    'background_color_jackpot' => '#336699',
    'value_color_jackpot' => '#336699',
    'sidebar_color' => '#336699',
    'sidebar_color_dark' => '#336699',
    'background_base' => '#336699',
    'background_base_dark' => '#336699',
    'background_color_cassino' => '#336699',

    'search_border_color' => '#80A9C4',  // Tom suave de azul
    'borders_and_dividers_colors' => '#80A9C4'
],
'blue_light' => [
    'Border_bottons_and_selected' => '#267E9E',  // Tom mais escuro de azul
    'bonus_color_dep' => '#267E9E',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#267E9E',
    'botao_deposito_background_nav' => '#267E9E',
    'botao_login_background_nav' => '#267E9E',
    'botao_login_background_modal' => '#267E9E',
    'botao_registro_background_modal' => '#267E9E',
    'botao_registro_border_nav' => '#267E9E',
    'botao_deposito_border_saq' => '#267E9E',
    'botao_deposito_background_dep' => '#267E9E',
    'botao_deposito_border_dep' => '#267E9E',
    'botao_deposito_background_saq' => '#267E9E',

    'search_back' => '#3399CC',  // Tom principal de azul
    'placeholder_background' => '#3399CC',
    'card_transaction' => '#3399CC',
    'background_color_category' => '#3399CC',
    'background_bottom_navigation' => '#3399CC',
    'background_bottom_navigation_dark' => '#3399CC',
    'footer_color' => '#3399CC',
    'footer_color_dark' => '#3399CC',
    'side_menu' => '#3399CC',
    'side_menu_dark' => '#3399CC',
    'navtop_color' => '#3399CC',
    'navtop_color_dark' => '#3399CC',

    'item_sub_color' => '#66B8D9',  // Tom mais claro de azul
    'background_color_jackpot' => '#66B8D9',
    'value_color_jackpot' => '#66B8D9',
    'sidebar_color' => '#66B8D9',
    'sidebar_color_dark' => '#66B8D9',
    'background_base' => '#66B8D9',
    'background_base_dark' => '#66B8D9',
    'background_color_cassino' => '#66B8D9',

    'search_border_color' => '#99D1E6',  // Tom suave de azul
    'borders_and_dividers_colors' => '#99D1E6'
],
'purple_blue' => [
    'Border_bottons_and_selected' => '#4D1AC6',  // Tom mais escuro de roxo-azulado
    'bonus_color_dep' => '#4D1AC6',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#4D1AC6',
    'botao_deposito_background_nav' => '#4D1AC6',
    'botao_login_background_nav' => '#4D1AC6',
    'botao_login_background_modal' => '#4D1AC6',
    'botao_registro_background_modal' => '#4D1AC6',
    'botao_registro_border_nav' => '#4D1AC6',
    'botao_deposito_border_saq' => '#4D1AC6',
    'botao_deposito_background_dep' => '#4D1AC6',
    'botao_deposito_border_dep' => '#4D1AC6',
    'botao_deposito_background_saq' => '#4D1AC6',

    'search_back' => '#6633FF',  // Tom principal de roxo-azulado
    'placeholder_background' => '#6633FF',
    'card_transaction' => '#6633FF',
    'background_color_category' => '#6633FF',
    'background_bottom_navigation' => '#6633FF',
    'background_bottom_navigation_dark' => '#6633FF',
    'footer_color' => '#6633FF',
    'footer_color_dark' => '#6633FF',
    'side_menu' => '#6633FF',
    'side_menu_dark' => '#6633FF',
    'navtop_color' => '#6633FF',
    'navtop_color_dark' => '#6633FF',

    'item_sub_color' => '#7F4DFF',  // Tom mais claro de roxo-azulado
    'background_color_jackpot' => '#7F4DFF',
    'value_color_jackpot' => '#7F4DFF',
    'sidebar_color' => '#7F4DFF',
    'sidebar_color_dark' => '#7F4DFF',
    'background_base' => '#7F4DFF',
    'background_base_dark' => '#7F4DFF',
    'background_color_cassino' => '#7F4DFF',

    'search_border_color' => '#A58CFF',  // Tom suave de roxo-azulado
    'borders_and_dividers_colors' => '#A58CFF'
],
'light_purple' => [
    'Border_bottons_and_selected' => '#4D4DCC',  // Tom mais escuro de roxo-azulado
    'bonus_color_dep' => '#4D4DCC',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#4D4DCC',
    'botao_deposito_background_nav' => '#4D4DCC',
    'botao_login_background_nav' => '#4D4DCC',
    'botao_login_background_modal' => '#4D4DCC',
    'botao_registro_background_modal' => '#4D4DCC',
    'botao_registro_border_nav' => '#4D4DCC',
    'botao_deposito_border_saq' => '#4D4DCC',
    'botao_deposito_background_dep' => '#4D4DCC',
    'botao_deposito_border_dep' => '#4D4DCC',
    'botao_deposito_background_saq' => '#4D4DCC',

    'search_back' => '#6666FF',  // Tom principal de roxo-azulado
    'placeholder_background' => '#6666FF',
    'card_transaction' => '#6666FF',
    'background_color_category' => '#6666FF',
    'background_bottom_navigation' => '#6666FF',
    'background_bottom_navigation_dark' => '#6666FF',
    'footer_color' => '#6666FF',
    'footer_color_dark' => '#6666FF',
    'side_menu' => '#6666FF',
    'side_menu_dark' => '#6666FF',
    'navtop_color' => '#6666FF',
    'navtop_color_dark' => '#6666FF',

    'item_sub_color' => '#8080FF',  // Tom mais claro de roxo-azulado
    'background_color_jackpot' => '#8080FF',
    'value_color_jackpot' => '#8080FF',
    'sidebar_color' => '#8080FF',
    'sidebar_color_dark' => '#8080FF',
    'background_base' => '#8080FF',
    'background_base_dark' => '#8080FF',
    'background_color_cassino' => '#8080FF',

    'search_border_color' => '#B3B3FF',  // Tom suave de roxo-azulado
    'borders_and_dividers_colors' => '#B3B3FF'
],
'green_vibrant' => [
    'Border_bottons_and_selected' => '#2A9F4D',  // Tom mais escuro de verde
    'bonus_color_dep' => '#2A9F4D',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#2A9F4D',
    'botao_deposito_background_nav' => '#2A9F4D',
    'botao_login_background_nav' => '#2A9F4D',
    'botao_login_background_modal' => '#2A9F4D',
    'botao_registro_background_modal' => '#2A9F4D',
    'botao_registro_border_nav' => '#2A9F4D',
    'botao_deposito_border_saq' => '#2A9F4D',
    'botao_deposito_background_dep' => '#2A9F4D',
    'botao_deposito_border_dep' => '#2A9F4D',
    'botao_deposito_background_saq' => '#2A9F4D',

    'search_back' => '#33CC66',  // Tom principal de verde
    'placeholder_background' => '#33CC66',
    'card_transaction' => '#33CC66',
    'background_color_category' => '#33CC66',
    'background_bottom_navigation' => '#33CC66',
    'background_bottom_navigation_dark' => '#33CC66',
    'footer_color' => '#33CC66',
    'footer_color_dark' => '#33CC66',
    'side_menu' => '#33CC66',
    'side_menu_dark' => '#33CC66',
    'navtop_color' => '#33CC66',
    'navtop_color_dark' => '#33CC66',

    'item_sub_color' => '#66D99B',  // Tom mais claro de verde
    'background_color_jackpot' => '#66D99B',
    'value_color_jackpot' => '#66D99B',
    'sidebar_color' => '#66D99B',
    'sidebar_color_dark' => '#66D99B',
    'background_base' => '#66D99B',
    'background_base_dark' => '#66D99B',
    'background_color_cassino' => '#66D99B',

    'search_border_color' => '#99E6B3',  // Tom suave de verde
    'borders_and_dividers_colors' => '#99E6B3'
],
'teal' => [
    'Border_bottons_and_selected' => '#2A5959',  // Tom mais escuro de verde-azulado
    'bonus_color_dep' => '#2A5959',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#2A5959',
    'botao_deposito_background_nav' => '#2A5959',
    'botao_login_background_nav' => '#2A5959',
    'botao_login_background_modal' => '#2A5959',
    'botao_registro_background_modal' => '#2A5959',
    'botao_registro_border_nav' => '#2A5959',
    'botao_deposito_border_saq' => '#2A5959',
    'botao_deposito_background_dep' => '#2A5959',
    'botao_deposito_border_dep' => '#2A5959',
    'botao_deposito_background_saq' => '#2A5959',

    'search_back' => '#336666',  // Tom principal de verde-azulado
    'placeholder_background' => '#336666',
    'card_transaction' => '#336666',
    'background_color_category' => '#336666',
    'background_bottom_navigation' => '#336666',
    'background_bottom_navigation_dark' => '#336666',
    'footer_color' => '#336666',
    'footer_color_dark' => '#336666',
    'side_menu' => '#336666',
    'side_menu_dark' => '#336666',
    'navtop_color' => '#336666',
    'navtop_color_dark' => '#336666',

    'item_sub_color' => '#5D8989',  // Tom mais claro de verde-azulado
    'background_color_jackpot' => '#5D8989',
    'value_color_jackpot' => '#5D8989',
    'sidebar_color' => '#5D8989',
    'sidebar_color_dark' => '#5D8989',
    'background_base' => '#5D8989',
    'background_base_dark' => '#5D8989',
    'background_color_cassino' => '#5D8989',

    'search_border_color' => '#80B3B3',  // Tom suave de verde-azulado
    'borders_and_dividers_colors' => '#80B3B3'
],
'olive' => [
    'Border_bottons_and_selected' => '#5F5D2E',  // Tom mais escuro de oliva
    'bonus_color_dep' => '#5F5D2E',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#5F5D2E',
    'botao_deposito_background_nav' => '#5F5D2E',
    'botao_login_background_nav' => '#5F5D2E',
    'botao_login_background_modal' => '#5F5D2E',
    'botao_registro_background_modal' => '#5F5D2E',
    'botao_registro_border_nav' => '#5F5D2E',
    'botao_deposito_border_saq' => '#5F5D2E',
    'botao_deposito_background_dep' => '#5F5D2E',
    'botao_deposito_border_dep' => '#5F5D2E',
    'botao_deposito_background_saq' => '#5F5D2E',

    'search_back' => '#847348',  // Tom principal de oliva
    'placeholder_background' => '#847348',
    'card_transaction' => '#847348',
    'background_color_category' => '#847348',
    'background_bottom_navigation' => '#847348',
    'background_bottom_navigation_dark' => '#847348',
    'footer_color' => '#847348',
    'footer_color_dark' => '#847348',
    'side_menu' => '#847348',
    'side_menu_dark' => '#847348',
    'navtop_color' => '#847348',
    'navtop_color_dark' => '#847348',

    'item_sub_color' => '#B59B67',  // Tom mais claro de oliva
    'background_color_jackpot' => '#B59B67',
    'value_color_jackpot' => '#B59B67',
    'sidebar_color' => '#B59B67',
    'sidebar_color_dark' => '#B59B67',
    'background_base' => '#B59B67',
    'background_base_dark' => '#B59B67',
    'background_color_cassino' => '#B59B67',

    'search_border_color' => '#D0C79D',  // Tom suave de oliva
    'borders_and_dividers_colors' => '#D0C79D'
],
'oliva' => [
    'Border_bottons_and_selected' => '#5F5D2E',  // Tom mais escuro de oliva
    'bonus_color_dep' => '#5F5D2E',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#5F5D2E',
    'botao_deposito_background_nav' => '#5F5D2E',
    'botao_login_background_nav' => '#5F5D2E',
    'botao_login_background_modal' => '#5F5D2E',
    'botao_registro_background_modal' => '#5F5D2E',
    'botao_registro_border_nav' => '#5F5D2E',
    'botao_deposito_border_saq' => '#5F5D2E',
    'botao_deposito_background_dep' => '#5F5D2E',
    'botao_deposito_border_dep' => '#5F5D2E',
    'botao_deposito_background_saq' => '#5F5D2E',

    'search_back' => '#A69D84',  // Novo tom suave de oliva
    'placeholder_background' => '#A69D84',
    'card_transaction' => '#A69D84',
    'background_color_category' => '#A69D84',
    'background_bottom_navigation' => '#A69D84',
    'background_bottom_navigation_dark' => '#A69D84',
    'footer_color' => '#A69D84',
    'footer_color_dark' => '#A69D84',
    'side_menu' => '#A69D84',
    'side_menu_dark' => '#A69D84',
    'navtop_color' => '#A69D84',
    'navtop_color_dark' => '#A69D84',

    'item_sub_color' => '#B59B67',  // Tom mais claro de oliva
    'background_color_jackpot' => '#B59B67',
    'value_color_jackpot' => '#B59B67',
    'sidebar_color' => '#B59B67',
    'sidebar_color_dark' => '#B59B67',
    'background_base' => '#B59B67',
    'background_base_dark' => '#B59B67',
    'background_color_cassino' => '#B59B67',

    'search_border_color' => '#D0C79D',  // Tom suave de oliva
    'borders_and_dividers_colors' => '#D0C79D'
],
'oliva2' => [
    'Border_bottons_and_selected' => '#B55F09',  // Tom mais escuro (laranja-âmbar)
    'bonus_color_dep' => '#B55F09',
    'color_players' => '#B55F09',
    'back_sub_color' => '#B55F09',
    'botao_deposito_background_nav' => '#B55F09',
    'botao_login_background_nav' => '#B55F09',
    'botao_login_background_modal' => '#B55F09',
    'botao_registro_background_modal' => '#B55F09',
    'botao_registro_border_nav' => '#B55F09',
    'botao_deposito_border_saq' => '#B55F09',
    'botao_deposito_background_dep' => '#B55F09',
    'botao_deposito_border_dep' => '#B55F09',
    'botao_deposito_background_saq' => '#B55F09',

    'search_back' => '#D77E2A',  // Tom principal (laranja-âmbar suave)
    'placeholder_background' => '#D77E2A',
    'card_transaction' => '#D77E2A',
    'background_color_category' => '#D77E2A',
    'background_bottom_navigation' => '#D77E2A',
    'background_bottom_navigation_dark' => '#D77E2A',
    'footer_color' => '#D77E2A',
    'footer_color_dark' => '#D77E2A',
    'side_menu' => '#D77E2A',
    'side_menu_dark' => '#D77E2A',
    'navtop_color' => '#D77E2A',
    'navtop_color_dark' => '#D77E2A',

    'item_sub_color' => '#F2A040',  // Tom mais claro (laranja suave)
    'background_color_jackpot' => '#F2A040',
    'value_color_jackpot' => '#F2A040',
    'sidebar_color' => '#F2A040',
    'sidebar_color_dark' => '#F2A040',
    'background_base' => '#F2A040',
    'background_base_dark' => '#F2A040',
    'background_color_cassino' => '#F2A040',

    'search_border_color' => '#F6C084',  // Tom suave (bege claro)
    'borders_and_dividers_colors' => '#F6C084'
],
'doura' => [
    'Border_bottons_and_selected' => '#F4AC64',  // Tom laranja-âmbar suave
    'bonus_color_dep' => '#F4AC64',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#F4AC64',
    'botao_deposito_background_nav' => '#F4AC64',
    'botao_login_background_nav' => '#F4AC64',
    'botao_login_background_modal' => '#F4AC64',
    'botao_registro_background_modal' => '#F4AC64',
    'botao_registro_border_nav' => '#F4AC64',
    'botao_deposito_border_saq' => '#F4AC64',
    'botao_deposito_background_dep' => '#F4AC64',
    'botao_deposito_border_dep' => '#F4AC64',
    'botao_deposito_background_saq' => '#F4AC64',

    'search_back' => '#F5B57D',  // Tom suave de laranja-âmbar
    'placeholder_background' => '#F5B57D',
    'card_transaction' => '#F5B57D',
    'background_color_category' => '#F5B57D',
    'background_bottom_navigation' => '#F5B57D',
    'background_bottom_navigation_dark' => '#F5B57D',
    'footer_color' => '#F5B57D',
    'footer_color_dark' => '#F5B57D',
    'side_menu' => '#F5B57D',
    'side_menu_dark' => '#F5B57D',
    'navtop_color' => '#F5B57D',
    'navtop_color_dark' => '#F5B57D',

    'item_sub_color' => '#F7C78D',  // Tom mais claro de laranja-âmbar
    'background_color_jackpot' => '#F7C78D',
    'value_color_jackpot' => '#F7C78D',
    'sidebar_color' => '#F7C78D',
    'sidebar_color_dark' => '#F7C78D',
    'background_base' => '#F7C78D',
    'background_base_dark' => '#F7C78D',
    'background_color_cassino' => '#F7C78D',

    'search_border_color' => '#F8D4A0',  // Tom muito suave de laranja-dourado
    'borders_and_dividers_colors' => '#F8D4A0'
],
'amarelo2' => [
    'Border_bottons_and_selected' => '#D3AD06',  // Tom dourado forte
    'bonus_color_dep' => '#D3AD06',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#D3AD06',
    'botao_deposito_background_nav' => '#D3AD06',
    'botao_login_background_nav' => '#D3AD06',
    'botao_login_background_modal' => '#D3AD06',
    'botao_registro_background_modal' => '#D3AD06',
    'botao_registro_border_nav' => '#D3AD06',
    'botao_deposito_border_saq' => '#D3AD06',
    'botao_deposito_background_dep' => '#D3AD06',
    'botao_deposito_border_dep' => '#D3AD06',
    'botao_deposito_background_saq' => '#D3AD06',

    'search_back' => '#D5B722',  // Tom suave de dourado
    'placeholder_background' => '#D5B722',
    'card_transaction' => '#D5B722',
    'background_color_category' => '#D5B722',
    'background_bottom_navigation' => '#D5B722',
    'background_bottom_navigation_dark' => '#D5B722',
    'footer_color' => '#D5B722',
    'footer_color_dark' => '#D5B722',
    'side_menu' => '#D5B722',
    'side_menu_dark' => '#D5B722',
    'navtop_color' => '#D5B722',
    'navtop_color_dark' => '#D5B722',

    'item_sub_color' => '#D8C53F',  // Tom mais claro de dourado
    'background_color_jackpot' => '#D8C53F',
    'value_color_jackpot' => '#D8C53F',
    'sidebar_color' => '#D8C53F',
    'sidebar_color_dark' => '#D8C53F',
    'background_base' => '#D8C53F',
    'background_base_dark' => '#D8C53F',
    'background_color_cassino' => '#D8C53F',

    'search_border_color' => '#DBCE6E',  // Tom suave de dourado claro
    'borders_and_dividers_colors' => '#DBCE6E'
],
'amareloclaro' => [
    'Border_bottons_and_selected' => '#7E6A0A',  // Tom mais escuro de amarelo-ocre
    'bonus_color_dep' => '#7E6A0A',
    'color_players' => '#7E6A0A',
    'back_sub_color' => '#7E6A0A',
    'botao_deposito_background_nav' => '#7E6A0A',
    'botao_login_background_nav' => '#7E6A0A',
    'botao_login_background_modal' => '#7E6A0A',
    'botao_registro_background_modal' => '#7E6A0A',
    'botao_registro_border_nav' => '#7E6A0A',
    'botao_deposito_border_saq' => '#7E6A0A',
    'botao_deposito_background_dep' => '#7E6A0A',
    'botao_deposito_border_dep' => '#7E6A0A',
    'botao_deposito_background_saq' => '#7E6A0A',

    'search_back' => '#A88C12',  // Tom principal de amarelo-ocre
    'placeholder_background' => '#A88C12',
    'card_transaction' => '#A88C12',
    'background_color_category' => '#A88C12',
    'background_bottom_navigation' => '#A88C12',
    'background_bottom_navigation_dark' => '#A88C12',
    'footer_color' => '#A88C12',
    'footer_color_dark' => '#A88C12',
    'side_menu' => '#A88C12',
    'side_menu_dark' => '#A88C12',
    'navtop_color' => '#A88C12',
    'navtop_color_dark' => '#A88C12',

    'item_sub_color' => '#D1B235',  // Tom mais claro de amarelo-ocre
    'background_color_jackpot' => '#D1B235',
    'value_color_jackpot' => '#D1B235',
    'sidebar_color' => '#D1B235',
    'sidebar_color_dark' => '#D1B235',
    'background_base' => '#D1B235',
    'background_base_dark' => '#D1B235',
    'background_color_cassino' => '#D1B235',

    'search_border_color' => '#F1E04D',  // Tom suave de amarelo-ocre
    'borders_and_dividers_colors' => '#F1E04D'
],


'blackcize' => [
    'Border_bottons_and_selected' => '#8C8D8B',  // cor principal
    'bonus_color_dep' => '#8C8D8B',               // cor principal
    'color_players' => '#8C8D8B',                 // cor principal
    'back_sub_color' => '#8C8D8B',                // cor principal
    'botao_deposito_background_nav' => '#8C8D8B', // cor principal
    'botao_login_background_nav' => '#8C8D8B',    // cor principal
    'botao_login_background_modal' => '#8C8D8B',  // cor principal
    'botao_registro_background_modal' => '#8C8D8B', // cor principal
    'botao_registro_border_nav' => '#8C8D8B',      // cor principal
    'botao_deposito_border_saq' => '#8C8D8B',      // cor principal
    'botao_deposito_background_dep' => '#8C8D8B',  // cor principal
    'botao_deposito_border_dep' => '#8C8D8B',      // cor principal
    'botao_deposito_background_saq' => '#8C8D8B',  // cor principal

    'search_back' => '#161D29',                    // fundo de pesquisa
    'placeholder_background' => '#161D29',         // fundo do placeholder
    'card_transaction' => '#161D29',               // fundo do cartão de transação
    'background_color_category' => '#161D29',      // fundo da categoria
    'background_bottom_navigation' => '#161D29',   // fundo da navegação inferior
    'background_bottom_navigation_dark' => '#161D29', // fundo navegação inferior dark
    'footer_color' => '#161D29',                   // cor do rodapé
    'footer_color_dark' => '#161D29',              // cor do rodapé dark
    'side_menu' => '#161D29',                      // menu lateral
    'side_menu_dark' => '#161D29',                 // menu lateral dark
    'navtop_color' => '#161D29',                   // cor do topo
    'navtop_color_dark' => '#161D29',              // cor do topo dark

    'item_sub_color' => '#0D131C',                 // cor do item sub
    'background_color_jackpot' => '#0D131C',       // fundo jackpot
    'value_color_jackpot' => '#0D131C',            // valor jackpot
    'sidebar_color' => '#0D131C',                  // cor da sidebar
    'sidebar_color_dark' => '#0D131C',             // cor da sidebar dark
    'background_base' => '#0D131C',                // fundo base
    'background_base_dark' => '#0D131C',           // fundo base dark
    'background_color_cassino' => '#0D131C',       // fundo cassino

    'search_border_color' => '#6D7A91',            // cor da borda da pesquisa
    'borders_and_dividers_colors' => '#6D7A91'     // cor das bordas e divisores
],
'blackred' => [
    'Border_bottons_and_selected' => '#FF0000',  // vermelho
    'bonus_color_dep' => '#FF0000',               // vermelho
    'color_players' => '#F3F2F5',                 // vermelho
    'back_sub_color' => '#161D29',                // vermelho
    'botao_deposito_background_nav' => '#FF0000', // vermelho
    'botao_login_background_nav' => '#FF0000',    // vermelho
    'botao_login_background_modal' => '#FF0000',  // vermelho
    'botao_registro_background_modal' => '#FF0000', // vermelho
    'botao_registro_border_nav' => '#FF0000',      // vermelho
    'botao_deposito_border_saq' => '#FF0000',      // vermelho
    'botao_deposito_background_dep' => '#FF0000',  // vermelho
    'botao_deposito_border_dep' => '#FF0000',      // vermelho
    'botao_deposito_background_saq' => '#FF0000',  // vermelho

    'search_back' => '#161D29',                    // fundo de pesquisa
    'placeholder_background' => '#161D29',         // fundo do placeholder
    'card_transaction' => '#161D29',               // fundo do cartão de transação
    'background_color_category' => '#161D29',      // fundo da categoria
    'background_bottom_navigation' => '#161D29',   // fundo da navegação inferior
    'background_bottom_navigation_dark' => '#161D29', // fundo navegação inferior dark
    'footer_color' => '#161D29',                   // cor do rodapé
    'footer_color_dark' => '#161D29',              // cor do rodapé dark
    'side_menu' => '#161D29',                      // menu lateral
    'side_menu_dark' => '#161D29',                 // menu lateral dark
    'navtop_color' => '#161D29',                   // cor do topo
    'navtop_color_dark' => '#161D29',              // cor do topo dark

    'item_sub_color' => '#0D131C',                 // cor do item sub
    'background_color_jackpot' => '#0D131C',       // fundo jackpot
    'value_color_jackpot' => '#0D131C',            // valor jackpot
    'sidebar_color' => '#0D131C',                  // cor da sidebar
    'sidebar_color_dark' => '#0D131C',             // cor da sidebar dark
    'background_base' => '#0D131C',                // fundo base
    'background_base_dark' => '#0D131C',           // fundo base dark
    'background_color_cassino' => '#0D131C',       // fundo cassino

    'search_border_color' => '#6D7A91',            // cor da borda da pesquisa
    'borders_and_dividers_colors' => '#6D7A91'     // cor das bordas e divisores
],
'blackamarelo' => [
    'Border_bottons_and_selected' => '#F8D906',  // amarelo
    'bonus_color_dep' => '#F8D906',               // amarelo
    'color_players' => '#F3F2F5',                 // amarelo
    'back_sub_color' => '#0D131C',                // amarelo
    'botao_deposito_background_nav' => '#F8D906', // amarelo
    'botao_login_background_nav' => '#F8D906',    // amarelo
    'botao_login_background_modal' => '#F8D906',  // amarelo
    'botao_registro_background_modal' => '#F8D906', // amarelo
    'botao_registro_border_nav' => '#F8D906',      // amarelo
    'botao_deposito_border_saq' => '#F8D906',      // amarelo
    'botao_deposito_background_dep' => '#F8D906',  // amarelo
    'botao_deposito_border_dep' => '#F8D906',      // amarelo
    'botao_deposito_background_saq' => '#F8D906',  // amarelo

    'search_back' => '#161D29',                    // fundo de pesquisa
    'placeholder_background' => '#161D29',         // fundo do placeholder
    'card_transaction' => '#161D29',               // fundo do cartão de transação
    'background_color_category' => '#161D29',      // fundo da categoria
    'background_bottom_navigation' => '#161D29',   // fundo da navegação inferior
    'background_bottom_navigation_dark' => '#161D29', // fundo navegação inferior dark
    'footer_color' => '#161D29',                   // cor do rodapé
    'footer_color_dark' => '#161D29',              // cor do rodapé dark
    'side_menu' => '#161D29',                      // menu lateral
    'side_menu_dark' => '#161D29',                 // menu lateral dark
    'navtop_color' => '#161D29',                   // cor do topo
    'navtop_color_dark' => '#161D29',              // cor do topo dark

    'item_sub_color' => '#0D131C',                 // cor do item sub
    'background_color_jackpot' => '#0D131C',       // fundo jackpot
    'value_color_jackpot' => '#0D131C',            // valor jackpot
    'sidebar_color' => '#0D131C',                  // cor da sidebar
    'sidebar_color_dark' => '#0D131C',             // cor da sidebar dark
    'background_base' => '#0D131C',                // fundo base
    'background_base_dark' => '#0D131C',           // fundo base dark
    'background_color_cassino' => '#0D131C',       // fundo cassino

    'search_border_color' => '#6D7A91',            // cor da borda da pesquisa
    'borders_and_dividers_colors' => '#6D7A91'     // cor das bordas e divisores
],
'blackazul' => [
    'Border_bottons_and_selected' => '#0631E9',  // azul
    'bonus_color_dep' => '#0631E9',               // azul
    'color_players' => '#F3F2F5',                 // azul
    'back_sub_color' => '#161D29',                // fundo escuro
    'botao_deposito_background_nav' => '#0631E9', // azul
    'botao_login_background_nav' => '#0631E9',    // azul
    'botao_login_background_modal' => '#0631E9',  // azul
    'botao_registro_background_modal' => '#0631E9', // azul
    'botao_registro_border_nav' => '#0631E9',      // azul
    'botao_deposito_border_saq' => '#0631E9',      // azul
    'botao_deposito_background_dep' => '#0631E9',  // azul
    'botao_deposito_border_dep' => '#0631E9',      // azul
    'botao_deposito_background_saq' => '#0631E9',  // azul

    'search_back' => '#161D29',                    // fundo de pesquisa
    'placeholder_background' => '#161D29',         // fundo do placeholder
    'card_transaction' => '#161D29',               // fundo do cartão de transação
    'background_color_category' => '#161D29',      // fundo da categoria
    'background_bottom_navigation' => '#161D29',   // fundo da navegação inferior
    'background_bottom_navigation_dark' => '#161D29', // fundo navegação inferior dark
    'footer_color' => '#161D29',                   // cor do rodapé
    'footer_color_dark' => '#161D29',              // cor do rodapé dark
    'side_menu' => '#161D29',                      // menu lateral
    'side_menu_dark' => '#161D29',                 // menu lateral dark
    'navtop_color' => '#161D29',                   // cor do topo
    'navtop_color_dark' => '#161D29',              // cor do topo dark

    'item_sub_color' => '#0D131C',                 // cor do item sub
    'background_color_jackpot' => '#0D131C',       // fundo jackpot
    'value_color_jackpot' => '#0D131C',            // valor jackpot
    'sidebar_color' => '#0D131C',                  // cor da sidebar
    'sidebar_color_dark' => '#0D131C',             // cor da sidebar dark
    'background_base' => '#0D131C',                // fundo base
    'background_base_dark' => '#0D131C',           // fundo base dark
    'background_color_cassino' => '#0D131C',       // fundo cassino

    'search_border_color' => '#6D7A91',            // cor da borda da pesquisa
    'borders_and_dividers_colors' => '#6D7A91'     // cor das bordas e divisores
],
'cinza' => [
    'Border_bottons_and_selected' => '#333333',  // Tom mais escuro de cinza
    'bonus_color_dep' => '#333333',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#333333',
    'botao_deposito_background_nav' => '#333333',
    'botao_login_background_nav' => '#333333',
    'botao_login_background_modal' => '#333333',
    'botao_registro_background_modal' => '#333333',
    'botao_registro_border_nav' => '#333333',
    'botao_deposito_border_saq' => '#333333',
    'botao_deposito_background_dep' => '#333333',
    'botao_deposito_border_dep' => '#333333',
    'botao_deposito_background_saq' => '#333333',

    'search_back' => '#4D4D4D',  // Tom principal de cinza
    'placeholder_background' => '#4D4D4D',
    'card_transaction' => '#4D4D4D',
    'background_color_category' => '#4D4D4D',
    'background_bottom_navigation' => '#4D4D4D',
    'background_bottom_navigation_dark' => '#4D4D4D',
    'footer_color' => '#4D4D4D',
    'footer_color_dark' => '#4D4D4D',
    'side_menu' => '#4D4D4D',
    'side_menu_dark' => '#4D4D4D',
    'navtop_color' => '#4D4D4D',
    'navtop_color_dark' => '#4D4D4D',

    'item_sub_color' => '#808080',  // Tom mais claro de cinza
    'background_color_jackpot' => '#808080',
    'value_color_jackpot' => '#808080',
    'sidebar_color' => '#808080',
    'sidebar_color_dark' => '#808080',
    'background_base' => '#808080',
    'background_base_dark' => '#808080',
    'background_color_cassino' => '#808080',

    'search_border_color' => '#B3B3B3',  // Tom suave de cinza
    'borders_and_dividers_colors' => '#B3B3B3'
],
'cize2' => [
    'Border_bottons_and_selected' => '#4B4B38',  // Tom mais escuro de verde oliva
    'bonus_color_dep' => '#4B4B38',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#4B4B38',
    'botao_deposito_background_nav' => '#4B4B38',
    'botao_login_background_nav' => '#4B4B38',
    'botao_login_background_modal' => '#4B4B38',
    'botao_registro_background_modal' => '#4B4B38',
    'botao_registro_border_nav' => '#4B4B38',
    'botao_deposito_border_saq' => '#4B4B38',
    'botao_deposito_background_dep' => '#4B4B38',
    'botao_deposito_border_dep' => '#4B4B38',
    'botao_deposito_background_saq' => '#4B4B38',

    'search_back' => '#65654F',  // Tom principal de verde oliva
    'placeholder_background' => '#65654F',
    'card_transaction' => '#65654F',
    'background_color_category' => '#65654F',
    'background_bottom_navigation' => '#65654F',
    'background_bottom_navigation_dark' => '#65654F',
    'footer_color' => '#65654F',
    'footer_color_dark' => '#65654F',
    'side_menu' => '#65654F',
    'side_menu_dark' => '#65654F',
    'navtop_color' => '#65654F',
    'navtop_color_dark' => '#65654F',

    'item_sub_color' => '#7A7A5A',  // Tom mais claro de verde oliva
    'background_color_jackpot' => '#7A7A5A',
    'value_color_jackpot' => '#7A7A5A',
    'sidebar_color' => '#7A7A5A',
    'sidebar_color_dark' => '#7A7A5A',
    'background_base' => '#7A7A5A',
    'background_base_dark' => '#7A7A5A',
    'background_color_cassino' => '#7A7A5A',

    'search_border_color' => '#9A9A6F',  // Tom suave de verde oliva
    'borders_and_dividers_colors' => '#9A9A6F'
],
'vinho' => [
    'Border_bottons_and_selected' => '#1F0535',  // Tom mais escuro de roxo
    'bonus_color_dep' => '#1F0535',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#1F0535',
    'botao_deposito_background_nav' => '#1F0535',
    'botao_login_background_nav' => '#1F0535',
    'botao_login_background_modal' => '#1F0535',
    'botao_registro_background_modal' => '#1F0535',
    'botao_registro_border_nav' => '#1F0535',
    'botao_deposito_border_saq' => '#1F0535',
    'botao_deposito_background_dep' => '#1F0535',
    'botao_deposito_border_dep' => '#1F0535',
    'botao_deposito_background_saq' => '#1F0535',

    'search_back' => '#2C094B',  // Tom principal de roxo
    'placeholder_background' => '#2C094B',
    'card_transaction' => '#2C094B',
    'background_color_category' => '#2C094B',
    'background_bottom_navigation' => '#2C094B',
    'background_bottom_navigation_dark' => '#2C094B',
    'footer_color' => '#2C094B',
    'footer_color_dark' => '#2C094B',
    'side_menu' => '#2C094B',
    'side_menu_dark' => '#2C094B',
    'navtop_color' => '#2C094B',
    'navtop_color_dark' => '#2C094B',

    'item_sub_color' => '#5A1B6D',  // Tom mais claro de roxo
    'background_color_jackpot' => '#5A1B6D',
    'value_color_jackpot' => '#5A1B6D',
    'sidebar_color' => '#5A1B6D',
    'sidebar_color_dark' => '#5A1B6D',
    'background_base' => '#5A1B6D',
    'background_base_dark' => '#5A1B6D',
    'background_color_cassino' => '#5A1B6D',

    'search_border_color' => '#9B60A0',  // Tom suave de roxo
    'borders_and_dividers_colors' => '#9B60A0'
],
'vinho2' => [
    'Border_bottons_and_selected' => '#32087A',  // Tom mais escuro de roxo
    'bonus_color_dep' => '#32087A',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#32087A',
    'botao_deposito_background_nav' => '#32087A',
    'botao_login_background_nav' => '#32087A',
    'botao_login_background_modal' => '#32087A',
    'botao_registro_background_modal' => '#32087A',
    'botao_registro_border_nav' => '#32087A',
    'botao_deposito_border_saq' => '#32087A',
    'botao_deposito_background_dep' => '#32087A',
    'botao_deposito_border_dep' => '#32087A',
    'botao_deposito_background_saq' => '#32087A',

    'search_back' => '#410B8E',  // Tom principal de roxo
    'placeholder_background' => '#410B8E',
    'card_transaction' => '#410B8E',
    'background_color_category' => '#410B8E',
    'background_bottom_navigation' => '#410B8E',
    'background_bottom_navigation_dark' => '#410B8E',
    'footer_color' => '#410B8E',
    'footer_color_dark' => '#410B8E',
    'side_menu' => '#410B8E',
    'side_menu_dark' => '#410B8E',
    'navtop_color' => '#410B8E',
    'navtop_color_dark' => '#410B8E',

    'item_sub_color' => '#5C20A4',  // Tom mais claro de roxo
    'background_color_jackpot' => '#5C20A4',
    'value_color_jackpot' => '#5C20A4',
    'sidebar_color' => '#5C20A4',
    'sidebar_color_dark' => '#5C20A4',
    'background_base' => '#5C20A4',
    'background_base_dark' => '#5C20A4',
    'background_color_cassino' => '#5C20A4',

    'search_border_color' => '#9A4CBF',  // Tom suave de roxo
    'borders_and_dividers_colors' => '#9A4CBF'
],
'cinza1' => [
    'Border_bottons_and_selected' => '#1E2221',  // Tom mais escuro de cinza-escuro
    'bonus_color_dep' => '#1E2221',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#1E2221',
    'botao_deposito_background_nav' => '#1E2221',
    'botao_login_background_nav' => '#1E2221',
    'botao_login_background_modal' => '#1E2221',
    'botao_registro_background_modal' => '#1E2221',
    'botao_registro_border_nav' => '#1E2221',
    'botao_deposito_border_saq' => '#1E2221',
    'botao_deposito_background_dep' => '#1E2221',
    'botao_deposito_border_dep' => '#1E2221',
    'botao_deposito_background_saq' => '#1E2221',

    'search_back' => '#323637',  // Tom principal de cinza-esverdeado
    'placeholder_background' => '#323637',
    'card_transaction' => '#323637',
    'background_color_category' => '#323637',
    'background_bottom_navigation' => '#323637',
    'background_bottom_navigation_dark' => '#323637',
    'footer_color' => '#323637',
    'footer_color_dark' => '#323637',
    'side_menu' => '#323637',
    'side_menu_dark' => '#323637',
    'navtop_color' => '#323637',
    'navtop_color_dark' => '#323637',

    'item_sub_color' => '#4C5452',  // Tom mais claro de cinza-esverdeado
    'background_color_jackpot' => '#4C5452',
    'value_color_jackpot' => '#4C5452',
    'sidebar_color' => '#4C5452',
    'sidebar_color_dark' => '#4C5452',
    'background_base' => '#4C5452',
    'background_base_dark' => '#4C5452',
    'background_color_cassino' => '#4C5452',

    'search_border_color' => '#A1A8A6',  // Tom suave de cinza-esverdeado
    'borders_and_dividers_colors' => '#A1A8A6'
],
'aurora' => [
    'Border_bottons_and_selected' => '#016F66',  // Tom mais escuro de verde-azulado
    'bonus_color_dep' => '#016F66',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#016F66',
    'botao_deposito_background_nav' => '#016F66',
    'botao_login_background_nav' => '#016F66',
    'botao_login_background_modal' => '#016F66',
    'botao_registro_background_modal' => '#016F66',
    'botao_registro_border_nav' => '#016F66',
    'botao_deposito_border_saq' => '#016F66',
    'botao_deposito_background_dep' => '#016F66',
    'botao_deposito_border_dep' => '#016F66',
    'botao_deposito_background_saq' => '#016F66',

    'search_back' => '#027e7b',  // Tom principal de verde-azulado
    'placeholder_background' => '#027e7b',
    'card_transaction' => '#027e7b',
    'background_color_category' => '#027e7b',
    'background_bottom_navigation' => '#027e7b',
    'background_bottom_navigation_dark' => '#027e7b',
    'footer_color' => '#027e7b',
    'footer_color_dark' => '#027e7b',
    'side_menu' => '#027e7b',
    'side_menu_dark' => '#027e7b',
    'navtop_color' => '#027e7b',
    'navtop_color_dark' => '#027e7b',
    'item_sub_color' => '#3A9995',  // Tom mais claro de verde-azulado
    'background_color_jackpot' => '#3A9995',
    'value_color_jackpot' => '#3A9995',
    'sidebar_color' => '#3A9995',
    'sidebar_color_dark' => '#3A9995',
    'background_base' => '#3A9995',
    'background_base_dark' => '#3A9995',
    'background_color_cassino' => '#3A9995',
    'search_border_color' => '#6BB8B4',  // Tom suave de verde-azulado
    'borders_and_dividers_colors' => '#6BB8B4'
],
'purple_gray' => [
    'Border_bottons_and_selected' => '#4F3D46',  // Tom mais escuro de roxo-acinzentado
    'bonus_color_dep' => '#4F3D46',
    'color_players' => '#F3F2F5',
    'back_sub_color' => '#4F3D46',
    'botao_deposito_background_nav' => '#4F3D46',
    'botao_login_background_nav' => '#4F3D46',
    'botao_login_background_modal' => '#4F3D46',
    'botao_registro_background_modal' => '#4F3D46',
    'botao_registro_border_nav' => '#4F3D46',
    'botao_deposito_border_saq' => '#4F3D46',
    'botao_deposito_background_dep' => '#4F3D46',
    'botao_deposito_border_dep' => '#4F3D46',
    'botao_deposito_background_saq' => '#4F3D46',

    'search_back' => '#614f5c',  // Tom principal de roxo-acinzentado
    'placeholder_background' => '#614f5c',
    'card_transaction' => '#614f5c',
    'background_color_category' => '#614f5c',
    'background_bottom_navigation' => '#614f5c',
    'background_bottom_navigation_dark' => '#614f5c',
    'footer_color' => '#614f5c',
    'footer_color_dark' => '#614f5c',
    'side_menu' => '#614f5c',
    'side_menu_dark' => '#614f5c',
    'navtop_color' => '#614f5c',
    'navtop_color_dark' => '#614f5c',

    'item_sub_color' => '#7A6775',  // Tom mais claro de roxo-acinzentado
    'background_color_jackpot' => '#7A6775',
    'value_color_jackpot' => '#7A6775',
    'sidebar_color' => '#7A6775',
    'sidebar_color_dark' => '#7A6775',
    'background_base' => '#7A6775',
    'background_base_dark' => '#7A6775',
    'background_color_cassino' => '#7A6775',

    'search_border_color' => '#9F8D97',  // Tom suave de roxo-acinzentado
    'borders_and_dividers_colors' => '#9F8D97'
],
'forest_green' => [
    'Border_bottons_and_selected' => '#DABD01',  // Tom mais escuro de verde
    'bonus_color_dep' => '#103024',
    'color_players' => '#FFFFFF',
    'back_sub_color' => '#103024',
    'botao_deposito_background_nav' => '#DABD01',
    'botao_login_background_nav' => '#DABD01',
    'botao_login_background_modal' => '#103024',
    'botao_registro_background_modal' => '#DABD01',
    'botao_registro_border_nav' => '#DABD01',
    'botao_deposito_border_saq' => '#103024',
    'botao_deposito_background_dep' => '#DABD01',
    'botao_deposito_border_dep' => '#103024',
    'botao_deposito_background_saq' => '#103024',

    'search_back' => '#164633',  // Tom principal de verde escuro
    'placeholder_background' => '#164633',
    'card_transaction' => '#164633',
    'background_color_category' => '#1a4c38',
    'background_bottom_navigation' => '#164633',
    'background_bottom_navigation_dark' => '#164633',
    'footer_color' => '#164633',
    'footer_color_dark' => '#164633',
    'side_menu' => '#164633',
    'side_menu_dark' => '#26634a',
    'navtop_color' => '#164633',
    'navtop_color_dark' => '#164633',

    'item_sub_color' => '#2C6B5F',  // Tom mais claro de verde escuro
    'background_color_jackpot' => '#1e503c',
    'value_color_jackpot' => '#2C6B5F',
    'sidebar_color' => '#2C6B5F',
    'sidebar_color_dark' => '#1c533e',
    'background_base' => '#2C6B5F',
    'background_base_dark' => '#1c533e',
    'background_color_cassino' => '#1e503c',

    'search_border_color' => '#E80742',  // Tom suave de verde escuro
    'borders_and_dividers_colors' => '#4A8D7A'
],
'classica2' => [
    'Border_bottons_and_selected' => '#DABD01',  // Tom mais escuro de amarelo
    'bonus_color_dep' => '#DABD01',  
    'color_players' => '#FFFFFF',  // Cor dos jogadores em branco
    'back_sub_color' => '#000000',  // Cor de fundo em preto
    'botao_deposito_background_nav' => '#DABD01',  // Amarelo para botões de navegação
    'botao_login_background_nav' => '#DABD01',  // Amarelo para botões de navegação
    'botao_login_background_modal' => '#000000',  // Preto para fundo do modal
    'botao_registro_background_modal' => '#DABD01',  // Amarelo para fundo do modal
    'botao_registro_border_nav' => '#DABD01',  // Amarelo para bordas de navegação
    'botao_deposito_border_saq' => '#000000',  // Preto para bordas de depósito e saque
    'botao_deposito_background_dep' => '#DABD01',  // Amarelo para fundo de depósito
    'botao_deposito_border_dep' => '#000000',  // Preto para borda de depósito
    'botao_deposito_background_saq' => '#000000',  // Preto para fundo de saque

    'search_back' => '#000000',  // Tom principal de preto
    'placeholder_background' => '#000000',  // Preto para fundo de busca
    'card_transaction' => '#000000',  // Preto para fundo de transações
    'background_color_category' => '#000000',  // Preto para categoria de fundo
    'background_bottom_navigation' => '#000000',  // Preto para fundo de navegação inferior
    'background_bottom_navigation_dark' => '#000000',  // Preto para fundo de navegação inferior
    'footer_color' => '#000000',  // Preto para rodapé
    'footer_color_dark' => '#000000',  // Preto para rodapé escuro
    'side_menu' => '#000000',  // Preto para menu lateral
    'side_menu_dark' => '#716E6F',  // Preto para menu lateral escuro
    'navtop_color' => '#000000',  // Preto para a navegação superior
    'navtop_color_dark' => '#000000',  // Preto para navegação superior escura

    'item_sub_color' => '#5B5456',  // Amarelo para itens secundários
    'background_color_jackpot' => '#161113',  // Preto para fundo de jackpot
    'value_color_jackpot' => '#DABD01',  // Amarelo para valor do jackpot
    'sidebar_color' => '#000000',  // Preto para a barra lateral
    'sidebar_color_dark' => '#1A1819',  // Preto para a barra lateral escura
    'background_base' => '#000000',  // Preto para fundo base
    'background_base_dark' => '#000000',  // Preto para fundo base escuro
    'background_color_cassino' => '#161113',  // Preto para fundo de cassino

    'search_border_color' => '#E80742',  // Cor de borda suave em vermelho para destacar
    'borders_and_dividers_colors' => '#DABD01'  // Amarelo para divisores e bordas
],
'Roxa' => [
    'Border_bottons_and_selected' => '#4C024A',  // Tom mais escuro de roxo
    'bonus_color_dep' => '#4C024A',  
    'color_players' => '#F3F2F5',  // Cor dos jogadores em branco
    'back_sub_color' => '#4C024A',  // Preto para fundo
    'botao_deposito_background_nav' => '#4C024A',  // Tom escuro de roxo para fundo de navegação
    'botao_login_background_nav' => '#4C024A',  // Tom escuro de roxo para botões
    'botao_login_background_modal' => '#4C024A',  // Tom escuro de roxo para modal
    'botao_registro_background_modal' => '#4C024A',  // Tom escuro de roxo para modal
    'botao_registro_border_nav' => '#4C024A',  // Tom escuro de roxo para borda de navegação
    'botao_deposito_border_saq' => '#4C024A',  // Tom escuro de roxo para borda
    'botao_deposito_background_dep' => '#4C024A',  // Tom escuro de roxo para fundo de depósito
    'botao_deposito_border_dep' => '#4C024A',  // Tom escuro de roxo para borda de depósito
    'botao_deposito_background_saq' => '#4C024A',  // Tom escuro de roxo para fundo de saque

    'search_back' => '#740389',  // Tom principal de roxo intenso
    'placeholder_background' => '#740389',  // Tom principal de roxo para fundo de busca
    'card_transaction' => '#740389',  // Tom principal de roxo para transações
    'background_color_category' => '#740389',  // Tom principal de roxo para fundo de categoria
    'background_bottom_navigation' => '#740389',  // Tom principal de roxo para navegação inferior
    'background_bottom_navigation_dark' => '#740389',  // Tom principal de roxo para navegação inferior
    'footer_color' => '#740389',  // Tom principal de roxo para rodapé
    'footer_color_dark' => '#740389',  // Tom principal de roxo para rodapé escuro
    'side_menu' => '#740389',  // Tom principal de roxo para menu lateral
    'side_menu_dark' => '#740389',  // Tom principal de roxo para menu lateral escuro
    'navtop_color' => '#740389',  // Tom principal de roxo para a navegação superior
    'navtop_color_dark' => '#740389',  // Tom principal de roxo para navegação superior escura

    'item_sub_color' => '#9B4D85',  // Tom mais claro de roxo para itens secundários
    'background_color_jackpot' => '#9B4D85',  // Tom mais claro de roxo para jackpot
    'value_color_jackpot' => '#9B4D85',  // Tom mais claro de roxo para valor de jackpot
    'sidebar_color' => '#9B4D85',  // Tom mais claro de roxo para a barra lateral
    'sidebar_color_dark' => '#9B4D85',  // Tom mais claro de roxo para barra lateral escura
    'background_base' => '#9B4D85',  // Tom mais claro de roxo para fundo base
    'background_base_dark' => '#9B4D85',  // Tom mais claro de roxo para fundo base escuro
    'background_color_cassino' => '#9B4D85',  // Tom mais claro de roxo para fundo de cassino

    'search_border_color' => '#D25A8A',  // Tom suave de roxo para bordas de busca
    'borders_and_dividers_colors' => '#D25A8A'  // Tom suave de roxo para divisores e bordas
],
'brown_gold' => [
    'Border_bottons_and_selected' => '#4F3B12',  // Tom mais escuro de amarelo escuro (amarelo mostarda profundo)
    'bonus_color_dep' => '#4F3B12',  // Usado para bônus
    'color_players' => '#F3F2F5',  // Cor dos jogadores em branco (contraste claro)
    'back_sub_color' => '#4F3B12',  // Usado em fundos de seções secundárias
    'botao_deposito_background_nav' => '#4F3B12',  // Botões de navegação em amarelo escuro
    'botao_login_background_nav' => '#4F3B12',  // Botões de login
    'botao_login_background_modal' => '#4F3B12',  // Fundo de modal de login
    'botao_registro_background_modal' => '#4F3B12',  // Fundo de modal de registro
    'botao_registro_border_nav' => '#4F3B12',  // Bordas de registro
    'botao_deposito_border_saq' => '#4F3B12',  // Bordas de depósito
    'botao_deposito_background_dep' => '#4F3B12',  // Fundo de depósito
    'botao_deposito_border_dep' => '#4F3B12',  // Bordas de depósito
    'botao_deposito_background_saq' => '#4F3B12',  // Fundo de saque

    'search_back' => '#705714',  // Tom principal de amarelo escuro
    'placeholder_background' => '#705714',  // Tom de fundo para a busca
    'card_transaction' => '#705714',  // Card de transações
    'background_color_category' => '#705714',  // Cor de fundo de categoria
    'background_bottom_navigation' => '#705714',  // Fundo de navegação inferior
    'background_bottom_navigation_dark' => '#705714',  // Fundo de navegação inferior escura
    'footer_color' => '#705714',  // Cor de fundo do rodapé
    'footer_color_dark' => '#705714',  // Cor de fundo do rodapé escuro
    'side_menu' => '#705714',  // Cor do menu lateral
    'side_menu_dark' => '#705714',  // Cor do menu lateral escuro
    'navtop_color' => '#705714',  // Cor de navegação superior
    'navtop_color_dark' => '#705714',  // Cor de navegação superior escura

    'item_sub_color' => '#9B7A3B',  // Tom mais claro de amarelo escuro para itens secundários
    'background_color_jackpot' => '#9B7A3B',  // Cor para o fundo de jackpot
    'value_color_jackpot' => '#9B7A3B',  // Valor do jackpot em amarelo mais claro
    'sidebar_color' => '#9B7A3B',  // Cor de fundo da sidebar
    'sidebar_color_dark' => '#9B7A3B',  // Cor da sidebar escura
    'background_base' => '#9B7A3B',  // Cor base para fundo
    'background_base_dark' => '#9B7A3B',  // Cor base para fundo escuro
    'background_color_cassino' => '#9B7A3B',  // Cor de fundo para cassino

    'search_border_color' => '#D5A85D',  // Tom suave de amarelo para borda da busca
    'borders_and_dividers_colors' => '#D5A85D'  // Tom suave de amarelo para divisores e bordas
],
'lavender_grey' => [
    'Border_bottons_and_selected' => '#4E4A70',  // Tom mais escuro de roxo acinzentado
    'bonus_color_dep' => '#4E4A70',  // Cor escura para bônus
    'color_players' => '#F3F2F5',  // Cor dos jogadores em branco para contraste
    'back_sub_color' => '#4E4A70',  // Cor de fundo secundário mais escura
    'botao_deposito_background_nav' => '#4E4A70',  // Botões de navegação em roxo acinzentado
    'botao_login_background_nav' => '#4E4A70',  // Botões de login
    'botao_login_background_modal' => '#4E4A70',  // Fundo do modal de login
    'botao_registro_background_modal' => '#4E4A70',  // Fundo do modal de registro
    'botao_registro_border_nav' => '#4E4A70',  // Bordas de registro
    'botao_deposito_border_saq' => '#4E4A70',  // Bordas de depósito
    'botao_deposito_background_dep' => '#4E4A70',  // Fundo de depósito
    'botao_deposito_border_dep' => '#4E4A70',  // Bordas de depósito
    'botao_deposito_background_saq' => '#4E4A70',  // Fundo de saque

    'search_back' => '#6D6A9C',  // Tom principal de roxo acinzentado
    'placeholder_background' => '#6D6A9C',  // Tom de fundo de busca
    'card_transaction' => '#6D6A9C',  // Card de transações
    'background_color_category' => '#6D6A9C',  // Cor de fundo de categoria
    'background_bottom_navigation' => '#6D6A9C',  // Fundo de navegação inferior
    'background_bottom_navigation_dark' => '#6D6A9C',  // Fundo de navegação inferior escura
    'footer_color' => '#6D6A9C',  // Cor de fundo do rodapé
    'footer_color_dark' => '#6D6A9C',  // Cor de fundo do rodapé escuro
    'side_menu' => '#6D6A9C',  // Cor do menu lateral
    'side_menu_dark' => '#6D6A9C',  // Cor do menu lateral escuro
    'navtop_color' => '#6D6A9C',  // Cor de navegação superior
    'navtop_color_dark' => '#6D6A9C',  // Cor de navegação superior escura

    'item_sub_color' => '#8C8AA4',  // Tom mais claro de roxo acinzentado para itens secundários
    'background_color_jackpot' => '#8C8AA4',  // Cor para o fundo de jackpot
    'value_color_jackpot' => '#8C8AA4',  // Valor do jackpot em tom mais claro de roxo
    'sidebar_color' => '#8C8AA4',  // Cor de fundo da sidebar
    'sidebar_color_dark' => '#8C8AA4',  // Cor da sidebar escura
    'background_base' => '#8C8AA4',  // Cor base para fundo
    'background_base_dark' => '#8C8AA4',  // Cor base para fundo escuro
    'background_color_cassino' => '#8C8AA4',  // Cor de fundo para cassino

    'search_border_color' => '#A7A1C6',  // Tom suave de roxo para borda da busca
    'borders_and_dividers_colors' => '#A7A1C6'  // Tom suave de roxo para divisores e bordas
],
'green_teal' => [
    'Border_bottons_and_selected' => '#1E4B3A',  // Tom mais escuro de verde intenso
    'bonus_color_dep' => '#1E4B3A',  // Usado para bônus
    'color_players' => '#F3F2F5',  // Cor dos jogadores em branco para contraste
    'back_sub_color' => '#1E4B3A',  // Cor de fundo secundário mais escura
    'botao_deposito_background_nav' => '#1E4B3A',  // Botões de navegação em verde escuro
    'botao_login_background_nav' => '#1E4B3A',  // Botões de login
    'botao_login_background_modal' => '#1E4B3A',  // Fundo do modal de login
    'botao_registro_background_modal' => '#1E4B3A',  // Fundo do modal de registro
    'botao_registro_border_nav' => '#1E4B3A',  // Bordas de registro
    'botao_deposito_border_saq' => '#1E4B3A',  // Bordas de depósito
    'botao_deposito_background_dep' => '#1E4B3A',  // Fundo de depósito
    'botao_deposito_border_dep' => '#1E4B3A',  // Bordas de depósito
    'botao_deposito_background_saq' => '#1E4B3A',  // Fundo de saque

    'search_back' => '#285D48',  // Tom principal de verde escuro
    'placeholder_background' => '#285D48',  // Tom de fundo de busca
    'card_transaction' => '#285D48',  // Card de transações
    'background_color_category' => '#285D48',  // Cor de fundo de categoria
    'background_bottom_navigation' => '#285D48',  // Fundo de navegação inferior
    'background_bottom_navigation_dark' => '#285D48',  // Fundo de navegação inferior escura
    'footer_color' => '#285D48',  // Cor de fundo do rodapé
    'footer_color_dark' => '#285D48',  // Cor de fundo do rodapé escuro
    'side_menu' => '#285D48',  // Cor do menu lateral
    'side_menu_dark' => '#285D48',  // Cor do menu lateral escuro
    'navtop_color' => '#285D48',  // Cor de navegação superior
    'navtop_color_dark' => '#285D48',  // Cor de navegação superior escura

    'item_sub_color' => '#3C7C65',  // Tom mais claro de verde escuro para itens secundários
    'background_color_jackpot' => '#3C7C65',  // Cor para o fundo de jackpot
    'value_color_jackpot' => '#3C7C65',  // Valor do jackpot em tom mais claro de verde
    'sidebar_color' => '#3C7C65',  // Cor de fundo da sidebar
    'sidebar_color_dark' => '#3C7C65',  // Cor da sidebar escura
    'background_base' => '#3C7C65',  // Cor base para fundo
    'background_base_dark' => '#3C7C65',  // Cor base para fundo escuro
    'background_color_cassino' => '#3C7C65',  // Cor de fundo para cassino

    'search_border_color' => '#4C9B80',  // Tom suave de verde para borda da busca
    'borders_and_dividers_colors' => '#4C9B80'  // Tom suave de verde para divisores e bordas
],
'CQ9' => [            
            'Border_bottons_and_selected' => '#291F40',
            'bonus_color_dep' => '#291F40',
            'color_players' => '#291F40',
            'back_sub_color' => '#291F40',
            'botao_deposito_background_nav' => '#DABD01',
            'botao_login_background_nav' => '#DABD01',
            'botao_login_background_modal' => '#DABD01',
            'botao_registro_background_modal' => '#DABD01',
            'botao_registro_border_nav' => '#DABD01',
            'botao_deposito_border_saq' => '#DABD01',
            'botao_deposito_background_dep' => '#DABD01',
            'botao_deposito_border_dep' => '#DABD01',
            'botao_deposito_background_saq' => '#DABD01',

            'search_back' => '#58418C',
            'placeholder_background' => '#413363',
            'card_transaction' => '#58418C',
            'background_color_category' => '#2b294d',
            'background_bottom_navigation' => '#2c274e',
            'background_bottom_navigation_dark' => '#2c274e',
            'footer_color' => '#2c274e',
            'footer_color_dark' => '#2c274e',
            'side_menu' => '#2c274e',
            'side_menu_dark' => '#3c3467',
            'navtop_color' => '#2b294d',
            'navtop_color_dark' => '#2b294d',

            'item_sub_color' => '#2c274e',
            'background_color_jackpot' => '#2c274e',
            'value_color_jackpot' => '#2c274e',
            'sidebar_color' => '#2c274e',
            'sidebar_color_dark' => '#2c274e',
            'background_base' => '#2c274e',
            'background_base_dark' => '#2c274e',
            'background_color_cassino' => '#252346',

            'search_border_color' => '#8870B8',
            'borders_and_dividers_colors' => '#2c274e'                       
        ],

    ];

    if (array_key_exists($theme, $themes)) {
        $themeData = $themes[$theme];

        // Atualize o banco de dados com os valores do tema escolhido
        \DB::table('custom_layouts')->update([

            'Border_bottons_and_selected' => $themeData['Border_bottons_and_selected'],
            'bonus_color_dep' => $themeData['bonus_color_dep'],
            'color_players' => $themeData['color_players'],
            'back_sub_color' => $themeData['back_sub_color'],
            'botao_deposito_background_nav' => $themeData['botao_deposito_background_nav'],
            'botao_login_background_nav' => $themeData['botao_login_background_nav'],
            'botao_login_background_modal' => $themeData['botao_login_background_modal'],
            'botao_registro_background_modal' => $themeData['botao_registro_background_modal'],
            'botao_registro_border_nav' => $themeData['botao_registro_border_nav'],
            'botao_deposito_border_saq' => $themeData['botao_deposito_border_saq'],
            'botao_deposito_background_dep' => $themeData['botao_deposito_background_dep'],
            'botao_deposito_border_dep' => $themeData['botao_deposito_border_dep'],
            'botao_deposito_background_saq' => $themeData['botao_deposito_background_saq'],

            'search_back' => $themeData['search_back'],
            'placeholder_background' => $themeData['placeholder_background'],
            'card_transaction' => $themeData['card_transaction'],
            'background_color_category' => $themeData['background_color_category'],
            'background_bottom_navigation' => $themeData['background_bottom_navigation'],
            'background_bottom_navigation_dark' => $themeData['background_bottom_navigation_dark'],
            'footer_color' => $themeData['footer_color'],
            'footer_color_dark' => $themeData['footer_color_dark'],
            'side_menu' => $themeData['side_menu'],
            'side_menu_dark' => $themeData['side_menu_dark'],
            'navtop_color' => $themeData['navtop_color'],
            'navtop_color_dark' => $themeData['navtop_color_dark'],

            'item_sub_color' => $themeData['item_sub_color'],
            'background_color_jackpot' => $themeData['background_color_jackpot'],
            'value_color_jackpot' => $themeData['value_color_jackpot'],
            'sidebar_color' => $themeData['sidebar_color'],
            'sidebar_color_dark' => $themeData['sidebar_color_dark'],
            'background_base' => $themeData['background_base'],
            'background_base_dark' => $themeData['background_base_dark'],
            'background_color_cassino' => $themeData['background_color_cassino'],

            'search_border_color' => $themeData['search_border_color'],
            'borders_and_dividers_colors' => $themeData['borders_and_dividers_colors'],                  

        ]);

        return true;
    }

    return false;
}
}

return [
    'themes' => [
        'CQ9'=>'CQ9',
        'pourple' => 'Pourple',
        'black' => 'Black',
        'blue' => 'Blue',
        'red' => 'Red',
        'pink' => 'Pink',
        'limon' => 'Pimon',
        'brown' => 'Brown',
        'skyblue' => 'Skyblue',
        'whiteblue' => 'Whiteblue',
        'greendark' => 'Greendark',
        'orange' => 'Orange',
        'green' => 'Green',
        'yellow' => 'Yellow',
        'greenwhite' => 'Greenwhite',
        'gray' => 'Gray',
        'rose' => 'rose',
        'amarelo' => 'amarelo',
        'brown2' => 'brown2',
        'blue_purple' => 'blue_purple',
        'redvivo' => 'Vermelho Escuro',
        'Gold' => 'Gold',
        'bluesofic' => 'Blue Sofic',
        'blue_light' => 'Blue Light',
        'purple_blue' => 'Purple Blue',
        'light_purple' => 'Purple Light',
        'green_vibrant' => 'Green Vibrant',
        'teal' => 'Green Único',
        'olive' => 'Olive',
        'oliva' => 'Oliva',
        'oliva2' => 'Oliva 2',
         'doura' => 'Doura',
          'amarelo2' => 'Amarelo',
          'amareloclaro'=> 'Amarelo Claro',
          'blackcize'=> 'Preto com cinza',
          'blackred'=> 'Preto com vermelho',
          'blackamarelo'=> 'Preto com amarelo',
          'blackazul'=> 'balckazul',
          'amarelopreto'=> 'Amarelo com preto',
          'cinza1'=> 'Cinza Neutro',
           'cinza'=> 'Cinza',
           'cize2'=> 'cize2',
           'vinho'=>'Vinho',
           'vinho2'=>'Vinho 2',
           'aurora'=>'Aurora',
           'purple_gray'=>'Purple Gray',
           'forest_green'=>'Florest',
           'classica2'=>'classica Black',
           'Roxa'=>'Roxo',
           'brown_gold'=>'Gold Escuro',
           'lavender_grey'=>'Lavender Grey',
           'green_teal'=>'Green Teal'
    ],
];