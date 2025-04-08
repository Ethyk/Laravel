<script>
    export let selectedSalon;
    export let close;

    function handleClose() {
        close();
    }
</script>

<div class="modal">
    <div class="modal-content">
        <span class="close" onClick={handleClose}>&times;</span>
        <h2>{selectedSalon.name}</h2>
        <p><strong>Description:</strong> {selectedSalon.description}</p>
        <p><strong>Adresse:</strong> {selectedSalon.adresse}</p>
        <p><strong>Ville:</strong> {selectedSalon.ville}</p>
        <p><strong>Code Postal:</strong> {selectedSalon.code_postal}</p>
        <p><strong>Pays:</strong> {selectedSalon.pays}</p>
    </div>
</div>

<style>
    .modal {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 400px;
        position: relative;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
        font-size: 20px;
    }
</style>
