import { useEffect, useState } from "react";
import { useLocation } from "@remix-run/react";

interface SocialLink {
  name: string;
  url: string;
  icon: string;
  color: string;
}

interface SocialShareProps {
  title?: string;
  className?: string;
  iconSize?: "sm" | "md" | "lg";
  rounded?: boolean;
  compact?: boolean;
}

export function SocialShare({ 
  title, 
  className = "",
  iconSize = "md",
  rounded = true,
  compact = false
}: SocialShareProps) {
  const location = useLocation();
  const [currentUrl, setCurrentUrl] = useState("");

  useEffect(() => {
    // Get full URL including domain
    setCurrentUrl(window.location.origin + location.pathname);
  }, [location.pathname]);

  const socialLinks: SocialLink[] = [
    { 
      name: "Messenger", 
      url: `https://m.me/?share=${encodeURIComponent(currentUrl)}`, 
      icon: "/assets/icons/messenger.svg",
      color: "bg-blue-500"
    },
    { 
      name: "WhatsApp", 
      url: `https://wa.me/?text=${encodeURIComponent(title ? `${title} - ${currentUrl}` : currentUrl)}`,
      icon: "/assets/icons/whatsapp.svg",
      color: "bg-green-500"
    },
    { 
      name: "Facebook", 
      url: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`,
      icon: "/assets/icons/facebook.svg",
      color: "bg-blue-600"
    },
    { 
      name: "Twitter", 
      url: `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(title || '')}`,
      icon: "/assets/icons/twitter.svg",
      color: "bg-blue-400" 
    },
    { 
      name: "LinkedIn", 
      url: `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(currentUrl)}&title=${encodeURIComponent(title || '')}`,
      icon: "/assets/icons/linkedin.svg",
      color: "bg-blue-700" 
    },
  ];

  // Determine size classes based on iconSize prop
  const sizeClasses = {
    sm: "w-6 h-6",
    md: "w-8 h-8",
    lg: "w-10 h-10"
  }[iconSize];

  return (
    <div className={`flex ${compact ? 'space-x-1' : 'space-x-2'} items-center ${className}`}>
      {!compact && (
        <span className="text-sm text-muted-foreground mr-2">Partager :</span>
      )}
      
      {socialLinks.map((social) => (
        <a
          key={social.name}
          href={social.url}
          target="_blank"
          rel="noopener noreferrer"
          className={`
            ${sizeClasses}
            flex items-center justify-center
            ${rounded ? 'rounded-full' : 'rounded-md'}
            ${social.color}
            text-white
            hover:opacity-90 transition-opacity
          `}
          aria-label={`Partager sur ${social.name}`}
          title={`Partager sur ${social.name}`}
        >
          <span className="sr-only">Partager sur {social.name}</span>
          <img 
            src={social.icon}
            alt=""
            className={`${iconSize === 'sm' ? 'w-3 h-3' : iconSize === 'md' ? 'w-4 h-4' : 'w-5 h-5'} invert`}
            aria-hidden="true"
          />
        </a>
      ))}
    </div>
  );
}
