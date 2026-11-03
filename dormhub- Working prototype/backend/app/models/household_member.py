from sqlalchemy import Column, Integer, Enum, ForeignKey, TIMESTAMP, func, UniqueConstraint
from sqlalchemy.orm import relationship

from app.database import Base


class HouseholdMember(Base):
    __tablename__ = "household_members"
    __table_args__ = (UniqueConstraint("household_id", "user_id", name="unique_membership"),)

    id = Column(Integer, primary_key=True, index=True)
    household_id = Column(Integer, ForeignKey("households.id", ondelete="CASCADE"), nullable=False)
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    role = Column(Enum("owner", "member", name="member_role"), nullable=False, default="member")
    joined_at = Column(TIMESTAMP, server_default=func.now())

    household = relationship("Household", back_populates="members")
    user = relationship("User", back_populates="memberships")
