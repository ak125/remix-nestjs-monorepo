import { useEffect, useRef, useState } from "react";
import { useFetcher } from "@remix-run/react";

interface ChatMessage {
  id: string;
  content: string;
  isBot: boolean;
  createdAt: string;
}

export function Chatbot() {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const fetcher = useFetcher();
  const messagesEndRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (messagesEndRef.current) {
      messagesEndRef.current.scrollIntoView({ behavior: "smooth" });
    }
  }, [messages]);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const form = e.currentTarget;
    const input = form.elements.namedItem("message") as HTMLInputElement;
    
    if (!input.value.trim()) return;

    // Ajouter le message utilisateur
    setMessages(prev => [...prev, {
      id: Date.now().toString(),
      content: input.value,
      isBot: false,
      createdAt: new Date().toISOString()
    }]);

    // Envoyer au serveur
    fetcher.submit(
      { message: input.value },
      { method: "post", action: "/api/chat" }
    );

    input.value = "";
  };

  if (!isOpen) {
    return (
      <button
        onClick={() => setIsOpen(true)}
        className="fixed bottom-4 right-4 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600"
      >
        💬
      </button>
    );
  }

  return (
    <div className="fixed bottom-4 right-4 w-96 bg-white rounded-lg shadow-xl">
      <div className="p-4 border-b flex justify-between items-center">
        <h3 className="font-semibold">Support</h3>
        <button onClick={() => setIsOpen(false)}>✕</button>
      </div>

      <div className="h-96 overflow-y-auto p-4 space-y-4">
        {messages.map(message => (
          <div
            key={message.id}
            className={`flex ${
              message.isBot ? "justify-start" : "justify-end"
            }`}
          >
            <div
              className={`max-w-[80%] rounded-lg p-3 ${
                message.isBot
                  ? "bg-gray-100"
                  : "bg-blue-500 text-white"
              }`}
            >
              {message.content}
            </div>
          </div>
        ))}
        <div ref={messagesEndRef} />
      </div>

      <form onSubmit={handleSubmit} className="p-4 border-t">
        <div className="flex gap-2">
          <input
            type="text"
            name="message"
            placeholder="Tapez votre message..."
            className="flex-1 border rounded-lg px-3 py-2"
          />
          <button
            type="submit"
            className="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600"
          >
            Envoyer
          </button>
        </div>
      </form>
    </div>
  );
}
