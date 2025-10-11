# 3. Codebase Mind Map

This mind map provides a comprehensive overview of the project's codebase, including technologies, architecture, data flows, and AI integrations.

```mermaid
mindmap
  root(Social Networking PaaS)
    **Frontend**
      Framework: React
      Language: TypeScript
      Build Tool: Vite
      Styling: Tailwind CSS
      UI Components: shadcn/ui
      Routing: React Router
      State Management: @tanstack/react-query
      Form Handling: react-hook-form + zod
      **Key Directories**
        `src/pages`: Application pages
        `src/components`: Reusable components
        `src/hooks`: Custom React hooks
        `src/lib`: Utility functions

    **Backend (Supabase)**
      Platform: Backend-as-a-Service (BaaS)
      Database: PostgreSQL
      Authentication: Supabase Auth
      Serverless Functions: Supabase Functions
      **Key Directories**
        `supabase/functions`: Serverless functions for AI and business logic
        `supabase/migrations`: Database schema migrations

    **AI Integrations (Supabase Functions)**
      `ai-content-refiner`: Refines user-generated content
      `ai-digest`: Creates summaries or digests
      `ai-job-matcher`: Matches users with jobs
      `ai-meeting-notes`: Takes notes during meetings
      `ai-persona-chat`: Powers AI-based chat personas
      `ai-skill-extractor`: Extracts skills from user profiles
      `auto-tagger`: Automatically tags content
      `generate-transcript`: Generates transcripts from audio/video
      `speech-to-text`: Converts speech to text

    **Data Flows**
      **User Authentication**
        1. Frontend (Auth.tsx) -> Supabase Auth
        2. Supabase Auth -> Frontend (session)
      **Social Feed**
        1. Frontend (SocialFeed.tsx) -> Supabase (PostgreSQL)
        2. Supabase (PostgreSQL) -> Frontend (posts)
      **AI Feature (e.g., Content Refinement)**
        1. Frontend -> Supabase Function (ai-content-refiner)
        2. Supabase Function -> External AI Service (e.g., OpenAI)
        3. External AI Service -> Supabase Function
        4. Supabase Function -> Frontend

    **Monetization (Supabase Functions)**
      `create-payment`: Handles one-time payments
      `create-subscription`: Creates user subscriptions
      `check-subscription`: Checks a user's subscription status
      `create-tip`: Handles user tips
      `customer-portal`: Stripe customer portal integration

```

## Explanation

*   **Frontend:** The frontend is a modern React application with a strong emphasis on type safety (TypeScript) and a consistent UI (shadcn/ui). The use of `@tanstack/react-query` is a good choice for managing server state.
*   **Backend:** The backend is fully managed by Supabase, which simplifies infrastructure management. The use of serverless functions for business logic and AI integrations is a scalable approach.
*   **AI Integrations:** The platform heavily relies on AI for a variety of features. These are implemented as serverless functions that likely call external AI services.
*   **Data Flows:** The data flows are typical for a serverless application. The frontend interacts directly with the Supabase database and serverless functions.
*   **Monetization:** Monetization is a core part of the platform, with support for both subscriptions and one-time payments, likely integrated with Stripe through Supabase functions.
