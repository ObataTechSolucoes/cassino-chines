<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ([
            'CQ9' => ['name' => 'Classica', 'image' => 'classica.jpeg'],
               'forest_green' => ['name' => 'Classica', 'image' => 'classica.jpeg'],
               'classica2' => ['name' => 'Classica Black', 'image' => 'clasic2.jpeg'],
               'black' => ['name' => 'Preto com verde', 'image' => 'black.png'],
               'blackcize' => ['name' => 'Preto com cinza', 'image' => 'blackcize.jpeg'],
               'blackred' => ['name' => 'Preto com vermelho', 'image' => 'blackred.jpeg'],
                'blackamarelo' => ['name' => 'Preto com amarelo', 'image' => 'blackamarelo.jpeg'],
                'blackazul' => ['name' => 'Preto com azul', 'image' => 'balckazul.jpeg'],
               'brown' => ['name' => 'Marrom Escuro', 'image' => 'brown.png'],
               'brown2' => ['name' => 'Brown 2', 'image' => 'marrom2.jpeg'],
               'olive' => ['name' => 'Olive', 'image' => 'olive.jpeg'],
               'oliva' => ['name' => 'Oliva', 'image' => 'oliva.jpeg'],
               'red' => ['name' => 'Vermelho Escuro', 'image' => 'red.png'],
               'redvivo' => ['name' => 'Vermelho ', 'image' => 'vescuro.jpeg'],
               'amarelo' => ['name' => 'Vermelho Vivo', 'image' => 'redvivo.jpeg'],
               'rose' => ['name' => 'rose', 'image' => 'rose.jpeg'],
               'pink' => ['name' => 'Pink', 'image' => 'pink.png'],
               'blue' => ['name' => 'Blue', 'image' => 'blue.png'],
               'bluesofic' => ['name' => 'Blue Sofic', 'image' => 'buesofic.jpeg'],
               'pourple' => ['name' => 'Pourple', 'image' => 'pourple.png'],
               'purple_blue' => ['name' => 'Pourple Blue', 'image' => 'purple_blue.jpeg'],
               'light_purple' => ['name' => 'Pourple Light', 'image' => 'light_purple.jpeg'],
               'whiteblue' => ['name' => 'WhiteBlue', 'image' => 'whiteblue.png'],
               'blue_light' => ['name' => 'Blue Light', 'image' => 'blue_light.jpeg'],
               'skyblue' => ['name' => 'SkyBlue', 'image' => 'skyblue.png'],
               'greendark' => ['name' => 'GreenDark', 'image' => 'greendark.png'],
               'green' => ['name' => 'Green', 'image' => 'green.png'],
               'green_vibrant' => ['name' => 'Green Vibrant', 'image' => 'green_vibrant.jpeg'],
               'limon' => ['name' => 'Limon', 'image' => 'limon.png'],
               'greenwhite' => ['name' => 'Greenwhite', 'image' => 'greenwhite.png'],
               'aurora' => ['name' => 'Aurora', 'image' => 'aurora.jpeg'],
               'teal' => ['name' => 'Green Único', 'image' => 'greenunico.jpeg'],
               'blue_purple' => ['name' => 'graygreen', 'image' => 'purple3.jpeg'],
               'gray' => ['name' => 'Gray', 'image' => 'gray.png'],
               'Gold' => ['name' => 'Gold', 'image' => 'gold.jpeg'],
               'brown_gold' => ['name' => 'Grown Gold', 'image' => 'brown_gold.jpeg'],
               'orange' => ['name' => 'Orange', 'image' => 'orange.png'],
               'oliva2' => ['name' => 'Orange 2', 'image' => 'orange2.jpeg'],
               'doura' => ['name' => 'Dora', 'image' => 'doura.jpeg'],
               'yellow' => ['name' => 'Yellow', 'image' => 'yellow.png'],
               'amarelo2' => ['name' => 'Amarelo 2', 'image' => 'amarelo2.jpeg'],
               'amareloclaro' => ['name' => 'Amarelo Claro', 'image' => 'amareloo.jpeg'],
               'cinza1' => ['name' => 'Cinza', 'image' => 'cinza1.jpeg'],
               'cinza' => ['name' => 'Cinza Neutro', 'image' => 'cinza.jpeg'],
               'cize2' => ['name' => 'Cinza 2', 'image' => 'cinza2.jpeg'],
               'vinho' => ['name' => 'Vinho', 'image' => 'vinho.jpeg'],
               'vinho2' => ['name' => 'Vinho 2', 'image' => 'vinho2.jpeg'],
               'purple_gray' => ['name' => 'Purple Gray', 'image' => 'purplegray.jpeg'],
               'Roxa' => ['name' => 'Roxo', 'image' => 'roxa.jpeg'],
               'lavender_grey'=> ['name' => 'Lavender Grey', 'image' => 'lavender_grey.jpeg'],
               'green_teal'=> ['name' => 'Lavender Grey', 'image' => 'lavender_grey.jpeg'],
               
               
               
                
                
                
                
                 
            ] as $key => $theme)
                <div class="border p-4 rounded text-center">
                    <h3 class="font-semibold text-lg mb-2">{{ $theme['name'] }}</h3>
                    <img src="{{ asset('storage/themes/' . $theme['image']) }}" alt="{{ $theme['name'] }}" class="mx-auto my-2 h-20 w-20 object-cover rounded-md">
                    <input type="radio" name="selectedTheme" value="{{ $key }}" wire:model="selectedTheme" />
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <x-filament::button type="submit">Aplicar Tema</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
