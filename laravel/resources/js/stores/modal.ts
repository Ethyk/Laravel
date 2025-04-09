// src/stores/modal.ts

import { writable } from 'svelte/store';

interface ModalState {
    id: string | null;
    params: Record<string, unknown>;
}

export const modalStore = writable<ModalState>({
    id: null,
    params: {}
});

export function openModal(id: string, params: Record<string, unknown> = {}) {
    modalStore.set({ id, params });
}

export function closeModal() {
    modalStore.set({ id: null, params: {} });
}