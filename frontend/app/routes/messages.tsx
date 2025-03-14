import { useEffect, useState } from 'react';
import { io } from 'socket.io-client';

const socket = io('http://localhost:3000');

export default function Messages() {
  const [messages, setMessages] = useState([]);

  useEffect(() => {
    fetch('/api/messages/USER_ID')
      .then((res) => res.json())
      .then((data) => setMessages(data));

    socket.on('newMessage', (message) => {
      setMessages((prev) => [message, ...prev]);
    });

    return () => socket.off('newMessage');
  }, []);

  return (
    <div>
      <h1>Messages</h1>
      <ul>
        {messages.map((msg) => (
          <li key={msg.id}>
            <strong>{msg.subject}</strong>: {msg.content}
          </li>
        ))}
      </ul>
    </div>
  );
}
