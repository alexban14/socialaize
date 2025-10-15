import { Button } from "@/components/ui/button";
import { UserPlus, MessageCircle } from "lucide-react";

interface InteractionButtonsProps {
  userId: string;
}

export function InteractionButtons({ userId }: InteractionButtonsProps) {
  return (
    <div className="flex items-center justify-center gap-2">
      <Button variant="outline" size="sm">
        <UserPlus className="w-3 h-3 mr-1" />
        Follow
      </Button>
      <Button size="sm">
        <MessageCircle className="w-3 h-3 mr-1" />
        Message
      </Button>
    </div>
  );
}
