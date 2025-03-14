import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface LiveShoppingState {
  currentSession: {
    id: string;
    productId: string;
    viewerCount: number;
  } | null;
  chat: Array<{
    id: string;
    message: string;
    userId: string;
    timestamp: number;
  }>;
  startSession: (productId: string) => void;
  endSession: () => void;
  addChatMessage: (message: string, userId: string) => void;
  updateViewerCount: (count: number) => void;
}

export const useLiveShoppingStore = create<LiveShoppingState>()(
  persist(
    (set) => ({
      currentSession: null,
      chat: [],

      startSession: (productId) =>
        set({
          currentSession: {
            id: crypto.randomUUID(),
            productId,
            viewerCount: 0
          },
          chat: []
        }),

      endSession: () =>
        set({
          currentSession: null,
          chat: []
        }),

      addChatMessage: (message, userId) =>
        set((state) => ({
          chat: [
            ...state.chat,
            {
              id: crypto.randomUUID(),
              message,
              userId,
              timestamp: Date.now()
            }
          ].slice(-100) // Keep last 100 messages
        })),

      updateViewerCount: (count) =>
        set((state) => ({
          currentSession: state.currentSession
            ? { ...state.currentSession, viewerCount: count }
            : null
        }))
    }),
    { name: 'live-shopping' }
  )
);
