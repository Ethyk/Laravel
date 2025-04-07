<script>
  import { Link, page, useForm } from '@inertiajs/svelte';
  import { router } from '@inertiajs/svelte';



    // Données pour le formulaire
    // let tatoueur = {
    //     name: '',
    //     email: '',
    //     phone: '',
    //     address: '',
    //     experience: '',
    //     bio: '',
    // };
    let { tatoueur:initdata, auth } = $props()
    // let { user } = auth; 
    const tatoueur = useForm({
        ...initdata,
        // bio: '',
        // style: JSON.stringify([]),
        // localisation_actuelle: '',
        // disponibilites:  JSON.stringify([]),
        // instagram: '',
        // _token: 'jeton_invalide', // Mauvais jeton
        _token: $page.props.csrf_token,
    });

    
    let salons = $derived($page.props.salons || []); 
    // const submitTatoueur = () => {
    //     router.post('/tatoueurs', tatoueur);
    // };

    console.log($page.props);



    const submitTatoueur = () => {
        $tatoueur.post(route('tatoueurs.store', tatoueur));
    };
    let errors = {}; // Pour stocker les erreurs de validation renvoyées par le backend

    // Fonction pour gérer la soumission du formulaire
    // const submitTatoueur = () => {
    //     router.post('/tatoueurs', tatoueur, {
    //         onSuccess: () => {
    //             alert('Profil Tatoueur créé avec succès !');
    //         },
    //         onError: (err) => {
    //             errors = err; // Capture les erreurs de validation
    //         },
    //     });
    // };
</script>

<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Créer un Profil Tatoueur</h1>

    <form on:submit|preventDefault={submitTatoueur} class="space-y-4">
        <div>
            <label for="bio" class="block text-sm font-medium">Bio complet</label>
            <input
                id="bio"
                type="text"
                bind:value={$tatoueur.bio}
                class="w-full border rounded px-4 py-2"
                placeholder="Bio complet"
            />
            {#if $tatoueur.errors.bio}
                <p class="text-red-500 text-sm mt-1">{$tatoueur.errors.bio}</p>
            {/if}
        </div>

        <div>
            <label for="style" class="block text-sm font-medium">style</label>
            <input
                id="style"
                type="text"
                bind:value={$tatoueur.style}
                class="w-full border rounded px-4 py-2"
                placeholder="style"
            />
            {#if $tatoueur.errors.style}
                <p class="text-red-500 text-sm mt-1">{$tatoueur.errors.style}</p>
            {/if}
        </div>

        <div>
            <label for="salon" class="block text-sm font-medium">Salon</label>
            <select
                id="salon"
                bind:value={$tatoueur.localisation_actuelle}
                class="w-full border rounded px-4 py-2"
            >
                <option value="" disabled selected>Choisissez un salon</option>
                {#each salons as salon}
                    <option class="text-gray-500" value={salon.id}>{salon.name}</option>
                {/each}
            </select>
            {#if $tatoueur.errors.localisation_actuelle}
                <p class="text-red-500 text-sm mt-1">{$tatoueur.errors.localisation_actuelle}</p>
            {/if}
            
        </div>

        <div>
            <label for="disponibilites" class="block text-sm font-medium">disponibilites</label>
            <input
                id="disponibilites"
                type="text"
                bind:value={$tatoueur.disponibilites}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse"
            />
            {#if $tatoueur.errors.disponibilites}
                <p class="text-red-500 text-sm mt-1">{$tatoueur.errors.disponibilites}</p>
            {/if}
        </div>

        <div>
            <label for="instagram" class="block text-sm font-medium">instagram</label>
            <input
                id="instagram"
                type="text"
                bind:value={$tatoueur.instagram}
                class="w-full border rounded px-4 py-2"
                placeholder="Années d'expérience"
            />
            {#if $tatoueur.errors.instagram}
                <p class="text-red-500 text-sm mt-1">{$tatoueur.errors.instagram}</p>
            {/if}
        </div>

       

        <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
            Soumettre
        </button>
    </form>

    <div class="mt-6">
        <a href="/tatoueurs" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour à la liste des Tatoueurs
        </a>
    </div>
</div>
