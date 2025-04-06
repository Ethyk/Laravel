<script>
    import { onMount } from "svelte";
    // import { Inertia } from '@inertiajs/inertia';
    import { inertia } from '@inertiajs/svelte';
    import { router } from '@inertiajs/svelte'


    let salon = {
        name: '',
        description:'',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',

    };

    const submitSalon = () => {
        router.post('/salons', salon);
    };

    // function submit(e) {
    //     e.preventDefault()
    //     router.post('/salons', salon)
    // }

</script>

<div class="container mx-auto">
    <h1 class="text-xl font-bold">Créer un salon</h1>
    <form on:submit|preventDefault={submitSalon} class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium">Nom du Salon</label>
            <input
                id="name"
                type="text"
                bind:value={salon.name}
                class="w-full border rounded px-4 py-2"
                placeholder="Nom du salon"
            />
        </div>
        <div>
            <label for="description" class="block text-sm font-medium">Description du Salon</label>
            <input
                id="description"
                type="text"
                bind:value={salon.description}
                class="w-full border rounded px-4 py-2"
                placeholder="Nom du salon"
            />
        </div>
        
        <div>
            <label for="adresse" class="block text-sm font-medium">Adresse</label>
            <input
                id="adresse"
                type="text"
                bind:value={salon.adresse}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse du salon"
            />
        </div>
        <div>
            <label for="ville" class="block text-sm font-medium">ville</label>
            <input
                id="ville"
                type="text"
                bind:value={salon.ville}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse du salon"
            />
        </div>
        <div>
            <label for="code_postal" class="block text-sm font-medium">code postal</label>
            <input
                id="code_postal"
                type="text"
                bind:value={salon.code_postal}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse du salon"
            />
        </div>
        <div>
            <label for="pays" class="block text-sm font-medium">pays</label>
            <input
                id="pays"
                type="text"
                bind:value={salon.pays}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse du salon"
            />
        </div>
        <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
            Soumettre
        </button>
    

    </form>
</div>
