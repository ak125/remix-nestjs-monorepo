import { useState } from 'react';
import { useFetcher } from '@remix-run/react';

interface Message {
  id: string;
  type: 'user' | 'bot';
  content: string;
}

export default function CarChatbot() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const fetcher = useFetcher();

  const handleSend = async () => {
    if (!input.trim()) return;

    const newMessage = {
      id: Date.now().toString(),
      type: 'user',
      content: input
    };

    setMessages(prev => [...prev, newMessage]);
    setInput('');

    fetcher.submit(
      { message: input },
      { method: 'post', action: '/api/chatbot' }
    );
  };

  return (
    <div className="chatbot-container border rounded-lg p-4">
      <div className="messages-container h-96 overflow-y-auto space-y-4">
        {messages.map(msg => (
          <div 
            key={msg.id}
            className={`p-3 rounded-lg ${
              msg.type === 'user' 
                ? 'bg-blue-100 ml-auto' 
                : 'bg-gray-100'
            }`}
          >
            {msg.content}
          </div>
        ))}
      </div>
      
      <div className="input-container mt-4 flex gap-2">
        <input
          type="text"
          value={input}
          onChange={e => setInput(e.target.value)}
          className="flex-1 p-2 border rounded"
          placeholder="Posez votre question..."
          onKeyPress={e => e.key === 'Enter' && handleSend()}
        />
        <button 
          onClick={handleSend}
          className="px-4 py-2 bg-blue-500 text-white rounded"
        >
          Envoyer
        </button>
      </div>
    </div>
  );
}
