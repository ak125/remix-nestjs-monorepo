import { useState } from 'react';
import { useFetcher } from '@remix-run/react';
import { StarIcon } from '@heroicons/react/20/solid';

interface Review {
  id: string;
  rating: number;
  comment: string;
  createdAt: string;
  user: {
    name: string;
  };
}

interface ReviewsListProps {
  modelId: number;
  reviews: Review[];
}

export default function ReviewsList({ modelId, reviews }: ReviewsListProps) {
  const [rating, setRating] = useState(0);
  const [comment, setComment] = useState('');
  const fetcher = useFetcher();

  const submitReview = () => {
    if (!rating || !comment) return;

    fetcher.submit(
      { rating: String(rating), comment, modelId: String(modelId) },
      { method: 'post', action: '/api/reviews' }
    );

    setRating(0);
    setComment('');
  };

  return (
    <div className="reviews-container space-y-6">
      <div className="add-review space-y-4">
        <h3 className="text-lg font-semibold">Ajouter un avis</h3>
        
        <div className="rating-input flex space-x-2">
          {[1,2,3,4,5].map(star => (
            <StarIcon 
              key={star}
              className={`w-6 h-6 cursor-pointer ${
                star <= rating ? 'text-yellow-400' : 'text-gray-300'
              }`}
              onClick={() => setRating(star)}
            />
          ))}
        </div>

        <textarea
          value={comment}
          onChange={e => setComment(e.target.value)}
          className="w-full p-2 border rounded"
          placeholder="Votre avis..."
        />

        <button
          onClick={submitReview}
          disabled={!rating || !comment}
          className="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50"
        >
          Publier
        </button>
      </div>

      <div className="reviews-list space-y-4">
        {reviews.map(review => (
          <div key={review.id} className="review p-4 border rounded">
            <div className="flex items-center space-x-2">
              <div className="rating flex">
                {[...Array(review.rating)].map((_, i) => (
                  <StarIcon key={i} className="w-5 h-5 text-yellow-400" />
                ))}
              </div>
              <span className="text-sm text-gray-600">
                par {review.user.name}
              </span>
            </div>
            <p className="mt-2">{review.comment}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
