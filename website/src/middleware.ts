import { NextResponse, NextRequest } from "next/server";

export function middleware(req: NextRequest) {
  // Force HTTPS in production
  if (process.env.NODE_ENV === "production" && req.headers.get("x-forwarded-proto") !== "https") {
    const url = req.nextUrl.clone();
    url.protocol = "https";
    return NextResponse.redirect(url);
  }
  return NextResponse.next();
}

export const config = {
  matcher: ["/(.*)"],
};