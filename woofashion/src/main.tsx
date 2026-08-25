import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import './app.css';

// Extract basePath from homeUrl (e.g. '/woofashion')
const homeUrl = window.wpData?.homeUrl || '';
const basePath = homeUrl ? new URL(homeUrl).pathname : '';

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BrowserRouter basename={basePath}>
      <App />
    </BrowserRouter>
  </StrictMode>
);
