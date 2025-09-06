<style>
    /* Esconde o menu lateral */
    .fi-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    /* Quando ativo, mostra o menu */
    .fi-sidebar.active {
        transform: translateX(0);
    }

    /* Botão de ativar menu */
    #menu-toggle {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #333;
        color: white;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1000;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menu = document.querySelector('.fi-sidebar');
        const toggleButton = document.createElement("button");
        toggleButton.id = "menu-toggle";
        toggleButton.innerText = "☰";
        document.body.appendChild(toggleButton);

        toggleButton.addEventListener("click", function () {
            menu.classList.toggle("active");
        });
    });
</script>
